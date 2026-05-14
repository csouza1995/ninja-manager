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

    public $dateOffset = 0;

    public $currentPeriodLabel = '';

    public $periodCount = 3;

    public function mount()
    {
        $this->calculateFinances();
    }

    public function setFilter($filter)
    {
        $this->activeFilter = $filter;
        $this->dateOffset = 0;
        $this->calculateFinances();
    }

    public function updatedPeriodCount()
    {
        $this->calculateFinances();
    }

    public function previousPeriod()
    {
        $this->dateOffset--;
        $this->calculateFinances();
    }

    public function nextPeriod()
    {
        $this->dateOffset++;
        $this->calculateFinances();
    }

    public function calculateFinances()
    {
        $now = Carbon::now()->startOfMonth();

        if ($this->activeFilter === 'month') {
            $now->addMonths($this->dateOffset);
        } elseif ($this->activeFilter === 'quarter') {
            $now->addQuarters($this->dateOffset);
        } else {
            $now->addYears($this->dateOffset);
        }

        // 1. Periods mapping grouped by type and ordered Past -> Present -> Future
        $periodsToCalculate = [];
        $half = floor($this->periodCount / 2);

        $dateRangeOffsets = range(-$half, $half);

        if ($this->activeFilter === 'month') {
            $this->currentPeriodLabel = ucfirst($now->translatedFormat('M/Y'));
            foreach ($dateRangeOffsets as $i) {
                $date = $now->copy()->addMonths($i);
                $label = $date->translatedFormat('M/Y');
                $periodsToCalculate[$label] = [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
            }
        } elseif ($this->activeFilter === 'quarter') {
            $this->currentPeriodLabel = $now->quarter.'ºTri/'.$now->year;
            foreach ($dateRangeOffsets as $i) {
                $date = $now->copy()->addQuarters($i);
                $start = $date->copy()->startOfQuarter();
                $end = $date->copy()->endOfQuarter();
                $label = ucfirst($start->translatedFormat('M')).'-'.ucfirst($end->translatedFormat('M/Y'));
                $periodsToCalculate[$label] = [$start, $end];
            }
        } else { // year
            $this->currentPeriodLabel = $now->format('Y');
            foreach ($dateRangeOffsets as $i) {
                $date = $now->copy()->addYears($i);
                $label = $date->format('Y');
                $periodsToCalculate[$label] = [$date->copy()->startOfYear(), $date->copy()->endOfYear()];
            }
        }

        $this->periods = [];
        $this->chartLabels = []; // Reset for new calculations
        $this->chartData = []; // Reset for new calculations

        foreach ($periodsToCalculate as $label => $range) {
            // Mês anterior para buscar encargos que vencem neste mês
            $prevMonthRange = [
                $range[0]->copy()->subMonth()->startOfMonth(),
                $range[0]->copy()->subMonth()->endOfMonth(),
            ];

            // IDs das Invoices do mês atual e do mês anterior
            $currentInvoiceIds = \App\Models\Invoice::whereBetween(DB::raw('COALESCE(competence_date, issued_at)'), $range)->select('id');
            $prevInvoiceIds = \App\Models\Invoice::whereBetween(DB::raw('COALESCE(competence_date, issued_at)'), $prevMonthRange)->select('id');
            $prevOutflowIds = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $prevMonthRange)->select('id');

            // === BLOCO 1: Recebimentos (Caixa) ===
            $billingPaidBase = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum('gross_amount');
            $billingPaidAdj = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNotNull('paid_at')->sum('adjustment_amount');
            $billingPaid = $billingPaidBase; // We show Base in the card

            $billingPendingBase = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum('gross_amount');
            $billingPendingAdj = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)->whereNull('paid_at')->sum('adjustment_amount');
            $billingPending = $billingPendingBase; // We show Base in the card

            // Encargos Futuros (reservar) - baseado no % de imposto de cada Revenue
            // Provisionado = tax dos Revenues já pagos neste mês
            $paidRevenues = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)
                ->whereNotNull('paid_at')
                ->with('invoice:id,amount,tax_amount')
                ->get();
            $reserveProvisioned = $paidRevenues->sum(function ($rev) {
                $rate = $rev->tax_percentage;
                if ($rate <= 0 && $rev->invoice && $rev->invoice->amount > 0) {
                    $rate = ($rev->invoice->tax_amount / $rev->invoice->amount) * 100;
                }

                return $rev->gross_amount * ($rate / 100);
            });

            // Em Aberto = tax dos Revenues pendentes neste mês
            $pendingRevenues = Revenue::whereBetween(DB::raw('COALESCE(paid_at, due_date)'), $range)
                ->whereNull('paid_at')
                ->with('invoice:id,amount,tax_amount')
                ->get();
            $reservePending = $pendingRevenues->sum(function ($rev) {
                $rate = $rev->tax_percentage;
                if ($rate <= 0 && $rev->invoice && $rev->invoice->amount > 0) {
                    $rate = ($rev->invoice->tax_amount / $rev->invoice->amount) * 100;
                }

                return $rev->gross_amount * ($rate / 100);
            });

            // Encargos para Retiradas (reservar) - 11% das retiradas de prolabore do mês atual
            $prolaboreCurrentMonth = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->where('type', 'Prolabore')
                ->sum('amount');
            $reserveOutflowTotal = $prolaboreCurrentMonth * 0.11;

            // === BLOCO 1.5: Faturamento do mês atual (informativo) ===
            $invoicedTotal = \App\Models\Invoice::whereBetween(DB::raw('COALESCE(competence_date, issued_at)'), $range)->sum('amount');
            // Imposto sobre Faturamento: calculado a partir dos % das Revenues vinculadas às Invoices do mês
            $currentInvoiceRevenues = Revenue::whereIn('invoice_id', $currentInvoiceIds)->get();
            $invoicedTaxTotal = $currentInvoiceRevenues->sum(function ($rev) {
                return $rev->gross_amount * ($rev->tax_percentage / 100);
            });
            $invoicedTaxRate = $invoicedTotal > 0 ? ($invoicedTaxTotal / $invoicedTotal) * 100 : 0;

            // === BLOCO 2: Encargos DAS (do mês ANTERIOR que vencem este mês) ===
            $prevInvoiceRevenuesQuery = Revenue::whereIn('invoice_id', $prevInvoiceIds);
            $prevInvoiceRevenues = (clone $prevInvoiceRevenuesQuery)->get();
            $prevRevenueIds = (clone $prevInvoiceRevenuesQuery)->select('id');

            $billingTaxTotal = $prevInvoiceRevenues->sum(function ($rev) {
                return $rev->gross_amount * ($rev->tax_percentage / 100);
            });

            $billingTaxPaid = Expenditure::where(function ($q) use ($prevInvoiceIds, $prevRevenueIds) {
                $q->where(fn ($q) => $q->where('model_type', 'App\Models\Invoice')->whereIn('model_id', $prevInvoiceIds))
                    ->orWhere(fn ($q) => $q->where('model_type', 'App\Models\Revenue')->whereIn('model_id', $prevRevenueIds));
            })->whereNotNull('paid_at')->sum(DB::raw('amount + adjustment_amount'));

            $billingTaxProvisioned = Expenditure::where(function ($q) use ($prevInvoiceIds, $prevRevenueIds) {
                $q->where(fn ($q) => $q->where('model_type', 'App\Models\Invoice')->whereIn('model_id', $prevInvoiceIds))
                    ->orWhere(fn ($q) => $q->where('model_type', 'App\Models\Revenue')->whereIn('model_id', $prevRevenueIds));
            })->whereNull('paid_at')->sum(DB::raw('amount + adjustment_amount'));

            $billingTaxPending = max(0, $billingTaxTotal - $billingTaxPaid - $billingTaxProvisioned);

            // === BLOCO 3: Despesas ===
            $expensesPaidBase = Expenditure::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNotNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'not like', '%Imposto%')
                        ->where('classification', 'not like', '%INSS%')
                        ->where('classification', 'not like', '%DAS%');
                })->sum('amount');
            $expensesPaidAdj = Expenditure::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNotNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'not like', '%Imposto%')
                        ->where('classification', 'not like', '%INSS%')
                        ->where('classification', 'not like', '%DAS%');
                })->sum('adjustment_amount');
            $expensesPaid = $expensesPaidBase;

            $expensesPendingBase = Expenditure::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'not like', '%Imposto%')
                        ->where('classification', 'not like', '%INSS%')
                        ->where('classification', 'not like', '%DAS%');
                })->sum('amount');
            $expensesPendingAdj = Expenditure::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'not like', '%Imposto%')
                        ->where('classification', 'not like', '%INSS%')
                        ->where('classification', 'not like', '%DAS%');
                })->sum('adjustment_amount');
            $expensesPending = $expensesPendingBase;

            // === BLOCO 4: Retiradas (Bruto = Soma das saídas com os impostos) ===
            $outflowsPaidBase = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNotNull('paid_at')
                ->sum('amount');
            $outflowsPaidAdj = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNotNull('paid_at')
                ->sum('adjustment_amount');
            $outflowsPaid = $outflowsPaidBase;

            $outflowsPendingBase = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNull('paid_at')
                ->sum('amount');
            $outflowsPendingAdj = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNull('paid_at')
                ->sum('adjustment_amount');
            $outflowsPending = $outflowsPendingBase;

            // Encargos Retiradas (mês anterior → este mês)
            $outflowTaxTotal = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $prevMonthRange)->sum('tax_amount');
            $outflowTaxPaid = Expenditure::where('model_type', 'App\Models\Outflow')
                ->whereIn('model_id', $prevOutflowIds)
                ->whereNotNull('paid_at')->sum(DB::raw('amount + adjustment_amount'));
            $outflowTaxProvisioned = Expenditure::where('model_type', 'App\Models\Outflow')
                ->whereIn('model_id', $prevOutflowIds)
                ->whereNull('paid_at')->sum(DB::raw('amount + adjustment_amount'));
            $outflowTaxPending = max(0, $outflowTaxTotal - $outflowTaxPaid - $outflowTaxProvisioned);

            // Pró-labore sugerido (maior entre 28% do faturamento e o salário mínimo vigente)
            $minimumWage = \App\Models\MinimumWage::getForDate($range[0]);
            $suggestedProlabore = max($invoicedTotal * 0.28, $minimumWage);
            $totalProlaborePaid = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->where('type', 'Prolabore')
                ->whereNotNull('paid_at')
                ->sum('amount');
            $totalProlaborePending = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->where('type', 'Prolabore')
                ->whereNull('paid_at')
                ->sum('amount');

            // Total Prolabore including adjustments for accurate status
            $totalProlaboreRealized = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->where('type', 'Prolabore')
                ->whereNotNull('paid_at')
                ->sum(DB::raw('amount + adjustment_amount'));

            $prolaboreStatus = 'error';
            if ($totalProlaborePaid >= $suggestedProlabore) {
                $prolaboreStatus = 'success';
            } elseif ($totalProlaborePaid + $totalProlaborePending >= $suggestedProlabore) {
                $prolaboreStatus = 'warning';
            }

            // Withdrawals Breakdown (Use Net amount - what actually goes to the person)
            $withdrawalsBreakdown = Outflow::whereBetween(DB::raw('COALESCE(reference_date, paid_at, due_date)'), $range)
                ->whereNotNull('person_name')
                ->select('person_name', DB::raw('SUM(amount + adjustment_amount - COALESCE(tax_amount, 0)) as total'))
                ->groupBy('person_name')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($item) => ['name' => $item->person_name, 'amount' => $item->total]);

            $this->periods[$label] = [
                // Recebimentos
                'billing_paid' => $billingPaid,
                'billing_pending' => $billingPending,

                // Encargos Futuros (reservar) - mês atual
                'reserve_provisioned' => $reserveProvisioned,
                'reserve_pending' => $reservePending,
                'reserve_total' => $reserveProvisioned + $reservePending,

                // Faturamento do mês atual
                'invoiced_total' => $invoicedTotal,
                'invoiced_tax_total' => $invoicedTaxTotal,
                'invoiced_tax_rate' => $invoicedTaxRate,

                // Faturamento (encargos mês anterior → este mês)
                'billing_tax_total' => $billingTaxTotal,
                'billing_tax_paid' => $billingTaxPaid,
                'billing_tax_provisioned' => $billingTaxProvisioned,
                'billing_tax_pending' => $billingTaxPending,

                // Despesas
                'expenses_paid' => $expensesPaid,
                'expenses_pending' => $expensesPending,

                // Retiradas
                'outflows_paid' => $outflowsPaid,
                'outflows_pending' => $outflowsPending,

                // Encargos Retiradas (INSS ref. mês anterior → este mês)
                'outflow_tax_total' => $outflowTaxTotal,
                'outflow_tax_paid' => $outflowTaxPaid,
                'outflow_tax_provisioned' => $outflowTaxProvisioned,
                'outflow_tax_pending' => $outflowTaxPending,

                // Reservas do mês atual
                'reserve_billing_total' => $reserveProvisioned + $reservePending,
                'reserve_outflow_total' => $reserveOutflowTotal,

                // Pró-labore sugerido
                'suggested_prolabore' => $suggestedProlabore,
                'prolabore_status' => $prolaboreStatus,
                'prolabore_paid' => $totalProlaborePaid,
                'prolabore_pending' => $totalProlaborePending,

                // Withdrawals Breakdown
                'withdrawals_breakdown' => $withdrawalsBreakdown,
            ];

            // Totais Referenciais
            $billing_total = $billingPaid + $billingPending;
            $expenses_total = $expensesPaid + $expensesPending;
            $outflows_total = $outflowsPaid + $outflowsPending;

            // SALDO PARCIAL ATUAL (Recebimentos - Reserva DAS - Despesas)
            // Includes Adjustments for accurate bank view
            $this->periods[$label]['partial_balance_actual'] = ($billingPaidBase + $billingPaidAdj)
                                                             - ($expensesPaidBase + $expensesPaidAdj)
                                                             - $reserveProvisioned;

            // SALDO PARCIAL PREVISTO (Recebimentos Totais - Reserva DAS Total - Despesas Totais)
            $this->periods[$label]['partial_balance_predicted'] = ($billingPaidBase + $billingPaidAdj + $billingPendingBase + $billingPendingAdj)
                                                                - ($expensesPaidBase + $expensesPaidAdj + $expensesPendingBase + $expensesPendingAdj)
                                                                - ($reserveProvisioned + $reservePending);

            // SALDO FINAL ATUAL (Saldo Parcial - Retiradas Brutas)
            // Nota: Retiradas Brutas já incluem os 11% de INSS (tax_amount)
            $this->periods[$label]['final_balance_actual'] = $this->periods[$label]['partial_balance_actual']
                                                           - ($outflowsPaidBase + $outflowsPaidAdj);

            // SALDO FINAL PREVISTO
            $this->periods[$label]['final_balance_predicted'] = $this->periods[$label]['partial_balance_predicted']
                                                              - ($outflowsPaidBase + $outflowsPaidAdj + $outflowsPendingBase + $outflowsPendingAdj);

            // Entradas/Saídas Previstas (Apenas o que afeta o lucro do mês)
            $this->periods[$label]['total_predicted_inflows'] = $billingPendingBase + $billingPendingAdj;
            $this->periods[$label]['total_predicted_outflows'] = ($expensesPendingBase + $expensesPendingAdj)
                                                               + ($outflowsPendingBase + $outflowsPendingAdj);
        }

        // Build pieData from computed periods
        $this->pieData = collect($this->periods)->map(function ($data, $label) {
            return [
                'label' => $label,
                'expenses' => round($data['expenses_paid'] + $data['expenses_pending'], 2),
                'taxes' => round($data['billing_tax_total'] + $data['outflow_tax_total'], 2),
                'withdrawals' => round($data['outflows_paid'] + $data['outflows_pending'], 2),
            ];
        })->values()->toArray();

        // 2. Bank Balances (CASH LOGIC with Tax Provision)
        $accounts = BankAccount::all();
        $this->bankBalances = [];
        foreach ($accounts as $account) {
            $balance = $account->calculateBalance();

            // Provisão de Imposto (Acumulado que ainda não foi pago desta conta)
            // 1. DAS Acumulado: Todas as receitas que já foram Faturadas OU já foram Recebidas
            $taxAccrued = Revenue::where('bank_account_id', $account->id)
                ->where(function ($q) {
                    $q->whereNotNull('paid_at')
                        ->orWhereNotNull('invoice_id');
                })
                ->get()
                ->sum(fn ($r) => round($r->gross_amount * ($r->tax_percentage / 100), 2));

            // 2. INSS Acumulado: Todos os pró-labores lançados que possuem despesa vinculada
            $outflowTaxAccrued = Outflow::where('origin_bank_account_id', $account->id)
                ->has('expenditure')
                ->get()
                ->sum('tax_amount');

            // 3. Tributos já Pagos (Saídas reais de impostos)
            $taxPaid = Expenditure::where('bank_account_id', $account->id)
                ->whereNotNull('paid_at')
                ->where(function ($q) {
                    $q->where('classification', 'like', '%Imposto%')
                        ->orWhere('classification', 'like', '%DAS%')
                        ->orWhere('classification', 'like', '%INSS%');
                })
                ->get()
                ->sum(fn ($e) => round($e->amount + $e->adjustment_amount, 2));

            // O imposto fica "bloqueado" aqui pois ainda não saiu via Expenditure
            $taxProvision = max(0, ($taxAccrued + $outflowTaxAccrued) - $taxPaid);

            $this->bankBalances[] = [
                'id' => $account->id,
                'name' => $account->nickname ?: $account->bank_name,
                'balance' => round((float) $balance, 2),
                'tax_provision' => round((float) $taxProvision, 2),
                'tax_provision_details' => [
                    'das_accrued' => round((float) $taxAccrued, 2),
                    'inss_accrued' => round((float) $outflowTaxAccrued, 2),
                    'taxes_paid' => round((float) $taxPaid, 2),
                ],
                'free_balance' => round((float) ($balance - $taxProvision), 2),
            ];
        }

        // 3. Chart Data (Matches the generated periods)
        $labels = [];
        $resultSeries = [];
        $revenueSeries = [];
        $expenseSeries = [];

        foreach ($this->periods as $label => $data) {
            $labels[] = $label;

            $revSum = $data['billing_paid'] + $data['billing_pending'];
            $expSum = $data['expenses_paid'] + $data['expenses_pending'];
            $reserveSum = $data['reserve_billing_total'] + $data['reserve_outflow_total'];
            $outSum = $data['outflows_paid'] + $data['outflows_pending'];

            $revenueSeries[] = $revSum;
            $expenseSeries[] = $expSum + $outSum + $reserveSum;
            $resultSeries[] = $data['final_balance_predicted'];
        }

        $this->chartData = [
            'labels' => $labels,
            'results' => $resultSeries,
            'revenues' => $revenueSeries,
            'expenses' => $expenseSeries,
        ];

        // 4. Flow Chart Data (Inflows vs Stacked Outflows - matches the generated periods)
        $flowLabels = [];
        $flowInflows = [];
        $flowExpenses = [];
        $flowTaxes = [];
        $flowWithdrawals = [];

        foreach ($this->periods as $label => $data) {
            $flowLabels[] = $label;

            $flowInflows[] = $data['billing_paid'] + $data['billing_pending'];
            $flowExpenses[] = $data['expenses_paid'] + $data['expenses_pending'];
            // Impostos no gráfico de fluxo agora mostram o que foi RESERVADO neste mês (Total previsto)
            $flowTaxes[] = $data['reserve_billing_total'] + $data['reserve_outflow_total'];
            $flowWithdrawals[] = $data['outflows_paid'] + $data['outflows_pending'];
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
