<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Expenditures;

use App\Models\Expenditure;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    #[Url]
    public ?int $showId = null;

    public function mount()
    {
        if ($this->showId) {
            $this->dispatch('open-expenditure-form', id: $this->showId, readOnly: true);
        }
    }

    public function create()
    {
        $this->dispatch('open-expenditure-form');
    }

    public function edit(int $id)
    {
        $this->dispatch('open-expenditure-form', id: $id);
    }

    public function updatedSearch()
    {
        // Search is reactive in child components
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $totalPending = Expenditure::whereNull('paid_at')
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $monthPending = Expenditure::whereNull('paid_at')
            ->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $taxNf = Expenditure::whereNull('paid_at')
            ->whereIn('model_type', ['App\Models\Revenue', 'App\Models\Invoice'])
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $taxProlabore = Expenditure::whereNull('paid_at')
            ->where('model_type', 'App\Models\Outflow')
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $nextMonthPending = Expenditure::whereNull('paid_at')
            ->whereBetween('due_date', [now()->addMonth()->startOfMonth(), now()->addMonth()->endOfMonth()])
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $taxOther = $totalPending - $taxNf - $taxProlabore;

        return view('livewire.pages.financial.expenditures.index', [
            'totalPending' => $totalPending,
            'monthPending' => $monthPending,
            'nextMonthPending' => $nextMonthPending,
            'taxNf' => $taxNf,
            'taxProlabore' => $taxProlabore,
            'taxOther' => $taxOther,
        ]);
    }
}
