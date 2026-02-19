<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\Client;
use App\Models\Executor;
use App\Models\Service;
use App\Models\ServiceItem;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        return [
            'negotiating' => Service::where('status', ServiceStatus::Negotiating)->count(),
            'approved' => Service::where('status', ServiceStatus::Approved)->count(),
            'in_progress' => Service::where('status', ServiceStatus::InProgress)->count(),
            'delivered' => Service::where('status', ServiceStatus::Delivered)->count(),
            'finalized_period' => Service::where('status', ServiceStatus::Finalized)
                ->where('finished_at', '>=', now()->subMonths(6))
                ->count(),
            'total_clients' => Client::count(),
            'total_executors' => Executor::count(),
            'total_items' => ServiceItem::count(),
        ];
    }

    #[Computed]
    public function recentServices()
    {
        return Service::with(['client', 'executor', 'role'])->latest()->take(5)->get();
    }

    #[Computed]
    public function recentClients()
    {
        return Client::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
