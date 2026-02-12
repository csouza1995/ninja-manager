<?php

namespace App\Livewire\Components\Services;

use App\Models\Service;
use App\Models\Client;
use App\Models\Executor;
use App\Models\Role;
use App\Models\ServiceItem;
use App\Models\ServiceItemPivot;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class Form extends Component
{
    public ?int $serviceId = null;
    public bool $showModal = false;
    public bool $readOnly = false;

    #[Validate('required')]
    public ?int $client_id = null;

    #[Validate('required')]
    public ?int $executor_id = null;

    #[Validate('required')]
    public ?int $role_id = null;

    #[Validate('required')]
    public string $status = 'negotiating';

    public bool $is_paid = false;
    public bool $is_documented = false;
    public bool $is_invoiced = false;
    public ?string $started_at = null;
    public ?string $finished_at = null;

    // Items management
    public array $items = [];
    public ?int $selectedServiceItemId = null;
    public $quantity = 1;

    #[Computed]
    public function service()
    {
        return $this->serviceId ? Service::with('items')->find($this->serviceId) : null;
    }

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get();
    }

    #[Computed]
    public function executors()
    {
        return Executor::orderBy('name')->get();
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    #[Computed]
    public function serviceItems()
    {
        return ServiceItem::orderBy('code')->get();
    }

    #[Computed]
    public function canEdit()
    {
        if (!$this->serviceId) return true;
        $service = $this->service;
        return $service ? $service->canEdit() : true;
    }

    #[On('create-service')]
    public function create()
    {
        $this->reset();
        $this->items = [];
        $this->readOnly = false;
        $this->showModal = true;
    }

    #[On('show-service')]
    public function show(int $id)
    {
        $this->edit($id);
        $this->readOnly = true;
    }

    #[On('duplicate-service')]
    public function duplicate(int $id)
    {
        $this->edit($id);
        $this->serviceId = null;
        $this->is_paid = false;
        $this->is_documented = false;
        $this->is_invoiced = false;
        $this->status = 'negotiating';
        $this->started_at = null;
        $this->finished_at = null;
        
        // Remove individual IDs from items so they are created as new
        foreach ($this->items as &$item) {
            $item['id'] = null;
        }
        
        $this->readOnly = false;
    }

    #[On('edit-service')]
    public function edit(int $id)
    {
        $this->readOnly = false;
        $this->serviceId = $id;
        $service = $this->service;

        if ($service) {
            $this->client_id = $service->client_id;
            $this->executor_id = $service->executor_id;
            $this->role_id = $service->role_id;
            $this->status = $service->status;
            $this->is_paid = $service->is_paid;
            $this->is_documented = $service->is_documented;
            $this->is_invoiced = $service->is_invoiced;
            $this->started_at = $service->started_at?->format('Y-m-d\TH:i');
            $this->finished_at = $service->finished_at?->format('Y-m-d\TH:i');

            // Load existing items
            $this->items = $service->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_item_id' => $item->service_item_id,
                    'code' => $item->code,
                    'description' => $item->description,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->total_price,
                ];
            })->toArray();
        }

        $this->showModal = true;
    }

    public function addItem()
    {
        if (!$this->selectedServiceItemId) return;

        $serviceItem = ServiceItem::find($this->selectedServiceItemId);
        if (!$serviceItem) return;

        $this->items[] = [
            'id' => null,
            'service_item_id' => $serviceItem->id,
            'code' => $serviceItem->code,
            'description' => $serviceItem->description,
            'unit_price' => $serviceItem->unit_price,
            'quantity' => $this->quantity,
            'total_price' => $serviceItem->unit_price * $this->quantity,
        ];

        $this->resetValidation('items');
        $this->selectedServiceItemId = null;
        $this->quantity = 1;
    }

    public function removeItem(int $index)
    {
        if (!$this->canEdit) return;
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
    }

    public function updateItemQuantity(int $index, $quantity)
    {
        if (!$this->canEdit || !isset($this->items[$index])) return;

        $this->items[$index]['quantity'] = (float)$quantity;
        $this->items[$index]['total_price'] = $this->items[$index]['unit_price'] * $quantity;
    }

    public function save()
    {
        if ($this->readOnly) return;
        $this->validate();

        if (empty($this->items)) {
            $this->addError('items', 'Adicione pelo menos um item ao serviço.');
            return;
        }

        $service = Service::updateOrCreate(
            ['id' => $this->serviceId],
            [
                'client_id' => $this->client_id,
                'executor_id' => $this->executor_id,
                'role_id' => $this->role_id,
                'status' => $this->status,
                'is_paid' => $this->is_paid,
                'is_documented' => $this->is_documented,
                'is_invoiced' => $this->is_invoiced,
                'started_at' => $this->started_at,
                'finished_at' => $this->finished_at,
            ]
        );

        // Update items only if service can be edited
        if ($service->canEdit()) {
            // Delete existing items
            $service->items()->delete();

            // Create new items
            foreach ($this->items as $item) {
                ServiceItemPivot::create([
                    'service_id' => $service->id,
                    'service_item_id' => $item['service_item_id'],
                    'code' => $item['code'],
                    'description' => $item['description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        $this->dispatch('service-saved');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->items = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.services.form');
    }
}
