<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Executor;
use App\Models\Role;
use App\Models\ServiceItem;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'executors' => Executor::count(),
            'roles' => Role::count(),
            'service_items' => ServiceItem::count(),
            'clients' => Client::count(),
        ];

        $recentClients = Client::latest()->take(5)->get();
        $recentItems = ServiceItem::latest()->take(5)->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentClients' => $recentClients,
            'recentItems' => $recentItems,
        ]);
    }
}
