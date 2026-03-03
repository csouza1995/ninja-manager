<?php

namespace App\Livewire\Pages\Financial;

use App\Models\BankAccount;
use App\Models\Expenditure;
use App\Models\Outflow;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $activeFilter = 'month'; // month, quarter, year

    public $chartType = 'result'; // result, comparison

    public $periods = [];

    public $bankBalances = [];

    public $chartData = [];

    public $chartLabels = [];

    public $flowChartData = [];

    public $pieData = [];

    public function mount()
    {
        $this->calculateFinances();
    }

    public function setFilter($filter)
    {
        $this->activeFilter = $filter;
        $this->calculateFinances();
    }

    public function calculateFinances()
    {
        $now = Carbon::now();

        // 1. Periods mapping grouped by type and ordered Past -> Present -> Future
        $periodsToCalculate = [];

        if ($this->activeFilter === 'month') {
            $range = [$now->copy()->subMonth(), $now, $now->copy()->addMonth()];
            foreach ($range as $date) {
                $label = $date->translatedFormat('M/Y');
                $periodsToCalculate[$label] = [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
            }
        } elseif ($this->activeFilter === 'quarter') {
            $range = [$now->copy()->subQuarter(), $now, $now->copy()->addQuarter()];
            foreach ($range as $date) {
                $start = $date->copy()->startOfQuarter();
                $end = $date->copy()->endOfQuarter();
                $label = ucfirst($start->translatedFormat('M')).'-'.ucfirst($end->translatedFormat('M/Y'));
                $periodsToCalculate[$label] = [$start, $end];
            }
        } else { // year
            $range = [$now->copy()->subYear(), $now, $now->copy()->addYear()];
            foreach ($range as $date) {
                $label = $date->format('Y');
                $periodsToCalculate[$label] = [$date->copy()->startOfYear(), $date->copy()->endOfYear()];
            }
        }

        $this->periods = [];
        $this->chartLabels = []; // Reset for new calculations
        $this->chartData = []; // Reset for new calculations

        foreach ($periodsToCalculate as $label => $range) {
            $this->periods[$label] = [
                // 1. Faturamento
                'billing_paid' => Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum('gross_amount'),
                'billing_pending' => Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum('gross_amount'),

                // 2. Encargos (Faturamento)
                'revenue_tax_paid' => Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum('tax_amount'),
                'revenue_tax_pending' => Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum('tax_amount'),

                // 3. Despesas
                'expenses_paid' => Expenditure::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)
                    ->whereNotNull('paid_at')
                    ->where(function ($q) {
                        $q->where('classification', 'not like', '%Imposto%')
                            ->where('classification', 'not like', '%INSS%')
                            ->where('classification', 'not like', '%DAS%');
                    })->sum('amount'),
                'expenses_pending' => Expenditure::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)
                    ->whereNull('paid_at')
                    ->where(function ($q) {
                        $q->where('classification', 'not like', '%Imposto%')
                            ->where('classification', 'not like', '%INSS%')
                            ->where('classification', 'not like', '%DAS%');
                    })->sum('amount'),

                // 4. Retiradas
                'outflows_paid' => Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum(DB::raw('amount - COALESCE(tax_amount, 0)')),
                'outflows_pending' => Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum(DB::raw('amount - COALESCE(tax_amount, 0)')),

                // 5. Encargos (Retiradas)
                'outflow_tax_paid' => Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum('tax_amount'),
                'outflow_tax_pending' => Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum('tax_amount'),

                // 6. Withdrawals Breakdown
                'withdrawals_breakdown' => Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)
                    ->whereNotNull('person_name')
                    ->select('person_name', DB::raw('SUM(amount - COALESCE(tax_amount, 0)) as total'))
                    ->groupBy('person_name')
                    ->orderByDesc('total')
                    ->get()
                    ->map(fn ($item) => ['name' => $item->person_name, 'amount' => $item->total]),
            ];

            // Totais Referenciais (para o Previsto)
            $billing_total = $this->periods[$label]['billing_paid'] + $this->periods[$label]['billing_pending'];
            $revenue_tax_provision = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->sum('tax_amount');
            $expenses_total = $this->periods[$label]['expenses_paid'] + $this->periods[$label]['expenses_pending'];
            $outflows_total = $this->periods[$label]['outflows_paid'] + $this->periods[$label]['outflows_pending'];
            $outflow_tax_provision = Outflow::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->sum('tax_amount');

            // SALDO PARCIAL ATUAL (O que já aconteceu de lucro operacional)
            $this->periods[$label]['partial_balance_actual'] = $this->periods[$label]['billing_paid']
                                                             - $this->periods[$label]['revenue_tax_paid']
                                                             - $this->periods[$label]['expenses_paid'];

            // SALDO PARCIAL PREVISTO (Expectativa operacional do período)
            $this->periods[$label]['partial_balance_predicted'] = $billing_total
                                                                - $revenue_tax_provision
                                                                - $expenses_total;

            // SALDO FINAL ATUAL (O que sobrou no caixa após retiradas realizadas)
            $this->periods[$label]['final_balance_actual'] = $this->periods[$label]['partial_balance_actual']
                                                           - $this->periods[$label]['outflows_paid']
                                                           - $this->periods[$label]['outflow_tax_paid'];

            // SALDO FINAL PREVISTO (O que se espera sobrar após tudo)
            $this->periods[$label]['final_balance_predicted'] = $this->periods[$label]['partial_balance_predicted']
                                                              - $outflows_total
                                                              - $outflow_tax_provision;
        }

        // Build pieData from computed periods
        $this->pieData = collect($this->periods)->map(function ($data, $label) {
            return [
                'label' => $label,
                'expenses' => round($data['expenses_paid'] + $data['expenses_pending'], 2),
                'taxes' => round($data['revenue_tax_paid'] + $data['revenue_tax_pending'] + $data['outflow_tax_paid'] + $data['outflow_tax_pending'], 2),
                'withdrawals' => round($data['outflows_paid'] + $data['outflows_pending'], 2),
            ];
        })->values()->toArray();

        // 2. Bank Balances (CASH LOGIC with Tax Provision)
        $accounts = BankAccount::all();
        $this->bankBalances = [];
        foreach ($accounts as $account) {
            $grossRev = Revenue::where('bank_account_id', $account->id)->whereNotNull('paid_at')->sum('gross_amount');
            $taxAccrued = Revenue::where('bank_account_id', $account->id)->whereNotNull('paid_at')->sum('tax_amount');

            // Add taxes accrued from Outflows (Prolabore)
            $outflowTaxAccrued = Outflow::where('origin_bank_account_id', $account->id)->whereNotNull('paid_at')->sum('tax_amount');

            $operExp = Expenditure::where('bank_account_id', $account->id)->whereNotNull('paid_at')->sum('amount');

            // How much tax was ALREADY paid from this bank?
            $taxPaid = Expenditure::where('bank_account_id', $account->id)
                ->where(function ($q) {
                    $q->where('classification', 'like', '%Imposto%')
                        ->orWhere('classification', 'like', '%INSS%')
                        ->orWhere('classification', 'like', '%DAS%');
                })
                ->whereNotNull('paid_at')
                ->sum('amount');

            $outflowExp = Outflow::where('origin_bank_account_id', $account->id)->whereNotNull('paid_at')->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));

            // Add transfers received in this account
            $transfersIn = Outflow::where('destination_bank_account_id', $account->id)
                ->where('type', 'Transferência')
                ->whereNotNull('paid_at')
                ->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));

            $balance = ($grossRev + $transfersIn) - ($operExp + $outflowExp);
            $taxProvision = max(0, ($taxAccrued + $outflowTaxAccrued) - $taxPaid);

            $this->bankBalances[] = [
                'name' => $account->nickname ?: $account->bank_name,
                'balance' => $balance,
                'tax_provision' => $taxProvision,
                'free_balance' => $balance - $taxProvision,
            ];
        }

        // 3. Chart Data (Last 6 months)
        $labels = [];
        $resultSeries = [];
        $revenueSeries = [];
        $expenseSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $labels[] = $m->translatedFormat('M/Y');

            $netRevenue = Revenue::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->sum('net_amount');

            $expSum = Expenditure::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->sum('amount');

            $outSum = Outflow::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));

            $resultSeries[] = $netRevenue - ($expSum + $outSum);
            $revenueSeries[] = $netRevenue;
            $expenseSeries[] = $expSum + $outSum;
        }

        $this->chartData = [
            'labels' => $labels,
            'results' => $resultSeries,
            'revenues' => $revenueSeries,
            'expenses' => $expenseSeries,
        ];

        // 4. Flow Chart Data (Last 6 months — inflows vs stacked outflows)
        $flowLabels = [];
        $flowInflows = [];
        $flowExpenses = [];
        $flowTaxes = [];
        $flowWithdrawals = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $flowLabels[] = $m->translatedFormat('M/Y');

            $flowInflows[] = Revenue::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->whereNotNull('paid_at')
                ->sum('gross_amount');

            $flowExpenses[] = Expenditure::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->whereNotNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'not like', '%Imposto%')
                        ->where('classification', 'not like', '%INSS%')
                        ->where('classification', 'not like', '%DAS%');
                })
                ->sum('amount');

            $revTax = Revenue::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->whereNotNull('paid_at')
                ->sum('tax_amount');

            $outTax = Outflow::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->whereNotNull('paid_at')
                ->sum('tax_amount');

            $flowTaxes[] = $revTax + $outTax;

            $flowWithdrawals[] = Outflow::whereMonth(DB::raw('COALESCE(paid_at, due_date)'), $m->month)
                ->whereYear(DB::raw('COALESCE(paid_at, due_date)'), $m->year)
                ->whereNotNull('paid_at')
                ->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));
        }

        $this->flowChartData = [
            'labels' => $flowLabels,
            'inflows' => $flowInflows,
            'expenses' => $flowExpenses,
            'taxes' => $flowTaxes,
            'withdrawals' => $flowWithdrawals,
        ];
    }

    public function render()
    {
        return view('livewire.pages.financial.dashboard')
            ->layout('components.layouts.app');
    }
}
