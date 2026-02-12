<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Executor;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceItem;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'negotiating' => Service::where('status', 'negotiating')->count(),
            'approved' => Service::where('status', 'approved')->count(),
            'in_progress' => Service::where('status', 'in_progress')->count(),
            'delivered' => Service::where('status', 'delivered')->count(),
            'finalized_period' => Service::where('status', 'finalized')
                ->where('finished_at', '>=', now()->subMonths(6))
                ->count(),
            'total_clients' => Client::count(),
            'total_executors' => Executor::count(),
            'total_items' => ServiceItem::count(),
        ];

        $recentServices = Service::with(['client', 'executor', 'role'])->latest()->take(5)->get();
        $recentClients = Client::latest()->take(5)->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentServices' => $recentServices,
            'recentClients' => $recentClients,
        ]);
    }
}
