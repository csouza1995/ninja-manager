<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientWorkHour;
use App\Models\Executor;
use App\Models\Expenditure;
use App\Models\Invoice;
use App\Models\Outflow;
use App\Models\Receipt;
use App\Models\Revenue;
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
            'version' => '2.0',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                // Master / Config tables
                'roles' => Role::all()->toArray(),
                'executors' => Executor::with('roles')->get()->map(function ($executor) {
                    return array_merge($executor->toArray(), [
                        'role_ids' => $executor->roles->pluck('id')->toArray(),
                    ]);
                })->toArray(),
                'service_items' => ServiceItem::all()->toArray(),
                'bank_accounts' => BankAccount::all()->toArray(),

                // Clients & Services
                'clients' => Client::all()->toArray(),
                'services' => Service::with('items')->get()->toArray(),
                'client_work_hours' => ClientWorkHour::with('services')->get()->map(function ($wh) {
                    return array_merge($wh->toArray(), [
                        'service_ids' => $wh->services->pluck('id')->toArray(),
                    ]);
                })->toArray(),
                'receipts' => Receipt::all()->toArray(),

                // Financial
                'invoices' => Invoice::all()->toArray(),
                'revenues' => Revenue::all()->toArray(),
                'expenditures' => Expenditure::all()->toArray(),
                'outflows' => Outflow::all()->toArray(),
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
            'bank_accounts' => 0,
            'clients' => 0,
            'services' => 0,
            'items_registered' => 0,
            'work_hours' => 0,
            'receipts' => 0,
            'invoices' => 0,
            'revenues' => 0,
            'expenditures' => 0,
            'outflows' => 0,
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

            // 4. Bank Accounts
            $bankAccountMapping = [];
            foreach ($data['bank_accounts'] ?? [] as $bankData) {
                $bank = BankAccount::updateOrCreate(
                    ['bank_name' => $bankData['bank_name'], 'owner_name' => $bankData['owner_name']],
                    $bankData
                );
                $bankAccountMapping[$bankData['id']] = $bank->id;
                $stats['bank_accounts']++;
            }

            // 5. Clients
            $clientMapping = [];
            foreach ($data['clients'] ?? [] as $clientData) {
                $client = Client::updateOrCreate(['document' => $clientData['document']], $clientData);
                $clientMapping[$clientData['id']] = $client->id;
                $stats['clients']++;
            }

            // 6. Services and nested items
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

            // 7. Client Work Hours
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

            // 8. Receipts
            foreach ($data['receipts'] ?? [] as $receiptData) {
                $serviceId = $serviceMapping[$receiptData['service_id']] ?? null;
                if (! $serviceId) {
                    continue;
                }

                Receipt::updateOrCreate(
                    ['receipt_number' => $receiptData['receipt_number']],
                    [
                        'service_id' => $serviceId,
                        'year' => $receiptData['year'],
                        'sequence' => $receiptData['sequence'],
                        'status' => $receiptData['status'],
                        'is_signed' => $receiptData['is_signed'] ?? false,
                        'is_sent' => $receiptData['is_sent'] ?? false,
                        'created_at' => $receiptData['created_at'],
                    ]
                );

                $stats['receipts']++;
            }

            // 9. Invoices
            $invoiceMapping = [];
            foreach ($data['invoices'] ?? [] as $invoiceData) {
                $clientId = $clientMapping[$invoiceData['client_id']] ?? null;
                $serviceId = isset($invoiceData['service_id']) ? ($serviceMapping[$invoiceData['service_id']] ?? null) : null;

                $invoice = Invoice::updateOrCreate(
                    ['access_key' => $invoiceData['access_key']],
                    [
                        'number' => $invoiceData['number'],
                        'client_id' => $clientId,
                        'service_id' => $serviceId,
                        'amount' => $invoiceData['amount'],
                        'tax_rate' => $invoiceData['tax_rate'] ?? null,
                        'issued_at' => $invoiceData['issued_at'],
                        'competence_date' => $invoiceData['competence_date'] ?? null,
                        'description' => $invoiceData['description'] ?? null,
                        'ctn' => $invoiceData['ctn'] ?? null,
                        'ctm' => $invoiceData['ctm'] ?? null,
                        'notes' => $invoiceData['notes'] ?? null,
                        'created_at' => $invoiceData['created_at'],
                    ]
                );

                $invoiceMapping[$invoiceData['id']] = $invoice->id;
                $stats['invoices']++;
            }

            // 10. Revenues
            $revenueMapping = [];
            foreach ($data['revenues'] ?? [] as $revenueData) {
                $bankAccountId = isset($revenueData['bank_account_id']) ? ($bankAccountMapping[$revenueData['bank_account_id']] ?? null) : null;
                $serviceId = isset($revenueData['service_id']) ? ($serviceMapping[$revenueData['service_id']] ?? null) : null;
                $invoiceId = isset($revenueData['invoice_id']) ? ($invoiceMapping[$revenueData['invoice_id']] ?? null) : null;

                $revenue = Revenue::create([
                    'service_id' => $serviceId,
                    'origin_name' => $revenueData['origin_name'] ?? null,
                    'description' => $revenueData['description'],
                    'classification' => $revenueData['classification'] ?? null,
                    'bank_account_id' => $bankAccountId,
                    'due_date' => $revenueData['due_date'],
                    'gross_amount' => $revenueData['gross_amount'],
                    'tax_percentage' => $revenueData['tax_percentage'] ?? 0,
                    'tax_amount' => $revenueData['tax_amount'] ?? 0,
                    'net_amount' => $revenueData['net_amount'],
                    'invoice_id' => $invoiceId,
                    'paid_at' => $revenueData['paid_at'] ?? null,
                    'notes' => $revenueData['notes'] ?? null,
                    'created_at' => $revenueData['created_at'],
                ]);

                $revenueMapping[$revenueData['id']] = $revenue->id;
                $stats['revenues']++;
            }

            // 11. Expenditures (handles morph remapping for Revenue)
            foreach ($data['expenditures'] ?? [] as $expenditureData) {
                $bankAccountId = isset($expenditureData['bank_account_id']) ? ($bankAccountMapping[$expenditureData['bank_account_id']] ?? null) : null;

                // Remap the morphable model_id
                $modelType = $expenditureData['model_type'] ?? null;
                $modelId = null;
                if ($modelType === 'App\\Models\\Revenue' && isset($expenditureData['model_id'])) {
                    $modelId = $revenueMapping[$expenditureData['model_id']] ?? null;
                }

                Expenditure::create([
                    'destination' => $expenditureData['destination'] ?? null,
                    'description' => $expenditureData['description'],
                    'classification' => $expenditureData['classification'] ?? null,
                    'bank_account_id' => $bankAccountId,
                    'due_date' => $expenditureData['due_date'],
                    'amount' => $expenditureData['amount'],
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'paid_at' => $expenditureData['paid_at'] ?? null,
                    'created_at' => $expenditureData['created_at'],
                ]);

                $stats['expenditures']++;
            }

            // 12. Outflows
            foreach ($data['outflows'] ?? [] as $outflowData) {
                $originBankId = isset($outflowData['origin_bank_account_id']) ? ($bankAccountMapping[$outflowData['origin_bank_account_id']] ?? null) : null;
                $destinationBankId = isset($outflowData['destination_bank_account_id']) ? ($bankAccountMapping[$outflowData['destination_bank_account_id']] ?? null) : null;

                Outflow::create([
                    'type' => $outflowData['type'],
                    'description' => $outflowData['description'] ?? null,
                    'origin_bank_account_id' => $originBankId,
                    'destination_bank_account_id' => $destinationBankId,
                    'person_name' => $outflowData['person_name'] ?? null,
                    'amount' => $outflowData['amount'],
                    'tax_percentage' => $outflowData['tax_percentage'] ?? 0,
                    'tax_amount' => $outflowData['tax_amount'] ?? 0,
                    'due_date' => $outflowData['due_date'],
                    'paid_at' => $outflowData['paid_at'] ?? null,
                    'created_at' => $outflowData['created_at'],
                ]);

                $stats['outflows']++;
            }
        });

        return $stats;
    }
}
