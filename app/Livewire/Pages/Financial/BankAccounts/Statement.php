<?php

namespace App\Livewire\Pages\Financial\BankAccounts;

use App\Models\BankAccount;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Statement extends Component
{
    public BankAccount $account;

    #[Url(except: '')]
    public string $search = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(BankAccount $bank_account): void
    {
        $this->account = $bank_account;
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function setFilter(string $period): void
    {
        switch ($period) {
            case 'today':
                $this->dateFrom = now()->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $this->dateFrom = now()->startOfYear()->format('Y-m-d');
                $this->dateTo = now()->endOfYear()->format('Y-m-d');
                break;
            case 'all':
                $this->dateFrom = '';
                $this->dateTo = '';
                break;
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function getConsolidatedBalanceProperty(): float
    {
        $limitDate = $this->dateTo ? ($this->dateTo.' 23:59:59') : null;

        return $this->account->calculateBalance($limitDate);
    }

    private function buildTransactionQuery(): Builder
    {
        $query1 = DB::table('revenues')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', 'origin_name as counterparty',
                DB::raw('gross_amount + adjustment_amount as amount'),
                'paid_at as date',
                DB::raw("'revenue' as type"),
                DB::raw("'plus' as icon"),
                DB::raw("'success' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('description', 'like', "%{$this->search}%")->orWhere('origin_name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo.' 23:59:59'));

        $query2 = DB::table('expenditures')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description',
                DB::raw("'Despesa' as counterparty"),
                DB::raw('amount + adjustment_amount as amount'),
                'paid_at as date',
                DB::raw("'expenditure' as type"),
                DB::raw("'minus' as icon"),
                DB::raw("'error' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo.' 23:59:59'));

        $query3 = DB::table('outflows')
            ->where('origin_bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', 'person_name as counterparty',
                DB::raw('(amount + adjustment_amount - COALESCE(tax_amount, 0)) as amount'),
                'paid_at as date',
                DB::raw("'outflow' as type"),
                DB::raw("CASE WHEN type IN ('Lucro/Dividendos', 'Prolabore') THEN 'withdraw' ELSE 'minus' END as icon"),
                DB::raw("'secondary' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('description', 'like', "%{$this->search}%")->orWhere('person_name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo.' 23:59:59'));

        $query4 = DB::table('outflows')
            ->where('destination_bank_account_id', $this->account->id)
            ->where('type', 'Transferência')
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description',
                DB::raw("'Transferência' as counterparty"),
                DB::raw('(amount + adjustment_amount - COALESCE(tax_amount, 0)) as amount'),
                'paid_at as date',
                DB::raw("'transfer' as type"),
                DB::raw("'plus' as icon"),
                DB::raw("'info' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo.' 23:59:59'));

        return $query1->unionAll($query2)->unionAll($query3)->unionAll($query4);
    }

    public function getGroupedTransactionsProperty(): Collection
    {
        $allTxns = $this->buildTransactionQuery()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        // Walk backwards from the closing balance to compute each day/month closing
        $runningBalance = $this->consolidatedBalance;
        $groups = [];
        $monthClosings = [];

        foreach ($allTxns->groupBy(fn ($t) => substr($t->date, 0, 10)) as $date => $dayTxns) {
            $dayClosing = $runningBalance;

            foreach ($dayTxns as $txn) {
                $runningBalance += $txn->icon === 'plus' ? -$txn->amount : $txn->amount;
            }

            $month = substr($date, 0, 7);

            if (! isset($monthClosings[$month])) {
                $monthClosings[$month] = $dayClosing;
            }

            $groups[] = [
                'date' => $date,
                'month' => $month,
                'transactions' => $dayTxns,
                'day_closing' => $dayClosing,
            ];
        }

        $seenMonths = [];

        foreach ($groups as &$g) {
            $g['is_month_end_row'] = ! in_array($g['month'], $seenMonths);
            $g['month_closing'] = $monthClosings[$g['month']];
            $seenMonths[] = $g['month'];
        }

        return collect($groups);
    }

    #[Layout('components.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.financial.bank-accounts.statement', [
            'groupedTransactions' => $this->groupedTransactions,
        ]);
    }
}
