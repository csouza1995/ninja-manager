<?php

namespace App\Livewire\Components\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

class Form extends Component
{
    public ?int $clientId = null;
    public bool $showModal = false;

    #[Validate('required|in:individual,company')]
    public string $type = 'individual';

    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('nullable|min:2')]
    public ?string $nickname = null;

    #[Validate('required')]
    public string $document = '';

    #[Validate('nullable')]
    public ?string $zip_code = null;

    #[Validate('nullable')]
    public ?string $street = null;

    #[Validate('nullable')]
    public ?string $number = null;

    #[Validate('nullable')]
    public ?string $complement = null;

    #[Validate('nullable')]
    public ?string $neighborhood = null;

    #[Validate('nullable')]
    public ?string $city = null;

    #[Validate('nullable|max:2')]
    public ?string $state = null;

    #[Computed]
    public function client()
    {
        return $this->clientId ? Client::find($this->clientId) : null;
    }

    #[On('create-client')]
    public function create()
    {
        $this->reset();
        $this->showModal = true;
    }

    #[On('edit-client')]
    public function edit(int $id)
    {
        $this->clientId = $id;
        $client = $this->client;
        
        if ($client) {
            $this->fill($client->only([
                'type', 'name', 'nickname', 'document', 'zip_code', 'street',
                'number', 'complement', 'neighborhood', 'city', 'state'
            ]));
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Client::updateOrCreate(
            ['id' => $this->clientId],
            [
                'type' => $this->type,
                'name' => $this->name,
                'nickname' => $this->nickname,
                'document' => $this->document,
                'zip_code' => $this->zip_code,
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'neighborhood' => $this->neighborhood,
                'city' => $this->city,
                'state' => $this->state,
            ]
        );

        $this->dispatch('client-saved');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.clients.form');
    }
}
