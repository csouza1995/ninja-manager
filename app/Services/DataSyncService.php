<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientWorkHour;
use App\Models\Executor;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\ServiceItemPivot;
use Illuminate\Support\Facades\DB;

class DataSyncService
{
    public function export(): array
    {
        return [
            'version' => '1.1',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'roles' => Role::all()->toArray(),
                'executors' => Executor::with('roles')->get()->map(function ($executor) {
                    return array_merge($executor->toArray(), [
                        'role_ids' => $executor->roles->pluck('id')->toArray(),
                    ]);
                })->toArray(),
                'service_items' => ServiceItem::all()->toArray(),
                'clients' => Client::all()->toArray(),
                'services' => Service::with('items')->get()->toArray(),
                'client_work_hours' => ClientWorkHour::with('services')->get()->map(function ($wh) {
                    return array_merge($wh->toArray(), [
                        'service_ids' => $wh->services->pluck('id')->toArray(),
                    ]);
                })->toArray(),
            ],
        ];
    }

    public function import(array $input): array
    {
        $data = $input['data'] ?? [];
        $stats = [
            'roles' => 0,
            'executors' => 0,
            'service_items' => 0,
            'clients' => 0,
            'services' => 0,
            'items_registered' => 0,
            'work_hours' => 0,
        ];

        DB::transaction(function () use ($data, &$stats) {
            // 1. Roles
            $roleMapping = [];
            foreach ($data['roles'] ?? [] as $roleData) {
                $role = Role::updateOrCreate(['name' => $roleData['name']], $roleData);
                $roleMapping[$roleData['id']] = $role->id;
                $stats['roles']++;
            }

            // 2. Executors
            $executorMapping = [];
            foreach ($data['executors'] ?? [] as $executorData) {
                $executor = Executor::updateOrCreate(['document' => $executorData['document']], [
                    'name' => $executorData['name'],
                ]);
                $executorMapping[$executorData['id']] = $executor->id;

                if (isset($executorData['role_ids'])) {
                    $newRoleIds = collect($executorData['role_ids'])->map(fn ($oldId) => $roleMapping[$oldId] ?? null)->filter()->toArray();
                    $executor->roles()->sync($newRoleIds);
                }

                $stats['executors']++;
            }

            // 3. Service Items (Master)
            foreach ($data['service_items'] ?? [] as $itemData) {
                ServiceItem::updateOrCreate(['code' => $itemData['code']], $itemData);
                $stats['service_items']++;
            }

            // 4. Clients
            $clientMapping = [];
            foreach ($data['clients'] ?? [] as $clientData) {
                $client = Client::updateOrCreate(['document' => $clientData['document']], $clientData);
                $clientMapping[$clientData['id']] = $client->id;
                $stats['clients']++;
            }

            // 5. Services and nested items
            $serviceMapping = [];
            foreach ($data['services'] ?? [] as $serviceData) {
                $client = Client::where('document', $data['clients'][array_search($serviceData['client_id'], array_column($data['clients'], 'id'))]['document'] ?? null)->first();
                $executor = Executor::where('document', $data['executors'][array_search($serviceData['executor_id'], array_column($data['executors'], 'id'))]['document'] ?? null)->first();
                $role = Role::where('name', $data['roles'][array_search($serviceData['role_id'], array_column($data['roles'], 'id'))]['name'] ?? null)->first();

                if (! $client || ! $executor || ! $role) {
                    continue;
                }

                $service = Service::create([
                    'client_id' => $client->id,
                    'executor_id' => $executor->id,
                    'role_id' => $role->id,
                    'status' => $serviceData['status'],
                    'is_paid' => $serviceData['is_paid'],
                    'is_documented' => $serviceData['is_documented'],
                    'is_invoiced' => $serviceData['is_invoiced'],
                    'started_at' => $serviceData['started_at'],
                    'finished_at' => $serviceData['finished_at'],
                    'created_at' => $serviceData['created_at'],
                ]);
                $serviceMapping[$serviceData['id']] = $service->id;

                // Import items
                foreach ($serviceData['items'] ?? [] as $pivotData) {
                    ServiceItemPivot::create([
                        'service_id' => $service->id,
                        'service_item_id' => null,
                        'code' => $pivotData['code'],
                        'description' => $pivotData['description'],
                        'unit_price' => $pivotData['unit_price'],
                        'quantity' => $pivotData['quantity'],
                        'total_price' => $pivotData['total_price'],
                    ]);

                    $exists = ServiceItem::where('code', $pivotData['code'])->exists();
                    if (! $exists) {
                        ServiceItem::create([
                            'code' => $pivotData['code'],
                            'description' => $pivotData['description'],
                            'unit_price' => $pivotData['unit_price'],
                        ]);
                        $stats['items_registered']++;
                    }
                }

                $stats['services']++;
            }

            // 6. Client Work Hours
            foreach ($data['client_work_hours'] ?? [] as $whData) {
                $clientId = $clientMapping[$whData['client_id']] ?? null;
                if (! $clientId) {
                    continue;
                }

                $workHour = ClientWorkHour::create([
                    'client_id' => $clientId,
                    'type' => $whData['type'],
                    'mode' => $whData['mode'],
                    'contract_type' => $whData['contract_type'] ?? 'fixed',
                    'contract_minutes' => $whData['contract_minutes'],
                    'executed_minutes' => $whData['executed_minutes'],
                    'hourly_rate' => $whData['hourly_rate'],
                    'weeks' => $whData['weeks'] ?? 0,
                    'days' => $whData['days'] ?? 0,
                    'hours' => $whData['hours'] ?? 0,
                    'minutes' => $whData['minutes'] ?? 0,
                    'contract_weeks' => $whData['contract_weeks'] ?? 0,
                    'contract_days' => $whData['contract_days'] ?? 0,
                    'contract_hours' => $whData['contract_hours'] ?? 0,
                    'contract_minutes_raw' => $whData['contract_minutes_raw'] ?? 0,
                    'base_d' => $whData['base_d'] ?? 8,
                    'base_w' => $whData['base_w'] ?? 5,
                    'notes' => $whData['notes'],
                    'created_at' => $whData['created_at'],
                ]);

                if (isset($whData['service_ids'])) {
                    $newServiceIds = collect($whData['service_ids'])
                        ->map(fn ($oldId) => $serviceMapping[$oldId] ?? null)
                        ->filter()
                        ->toArray();
                    $workHour->services()->sync($newServiceIds);
                }

                $stats['work_hours']++;
            }
        });

        return $stats;
    }
}
