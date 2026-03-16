<?php

namespace App\Livewire\Pages\Financial\BankAccounts;

use App\Models\BankAccount;
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

    public int $perPage = 20;

    public function mount(BankAccount $bank_account)
    {
        $this->account = $bank_account;
        // Default: Current Month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function setFilter(string $period)
    {
        $this->perPage = 20;

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

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->perPage = 20;
    }

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function getTransactionsProperty()
    {
        $query1 = DB::table('revenues')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', 'origin_name as counterparty', 'gross_amount as amount', 'paid_at as date',
                DB::raw("'revenue' as type"), DB::raw("'plus' as icon"), DB::raw("'success' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('description', 'like', "%{$this->search}%")->orWhere('origin_name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo . ' 23:59:59'));

        $query2 = DB::table('expenditures')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', DB::raw("'Despesa' as counterparty"), 'amount', 'paid_at as date',
                DB::raw("'expenditure' as type"), DB::raw("'minus' as icon"), DB::raw("'error' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo . ' 23:59:59'));

        $query3 = DB::table('outflows')
            ->where('origin_bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', 'person_name as counterparty', DB::raw('(amount - COALESCE(tax_amount, 0)) as amount'), 'paid_at as date',
                DB::raw("'outflow' as type"), DB::raw("CASE WHEN type IN ('Lucro/Dividendos', 'Prolabore') THEN 'withdraw' ELSE 'minus' END as icon"), DB::raw("'secondary' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('description', 'like', "%{$this->search}%")->orWhere('person_name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo . ' 23:59:59'));

        $query4 = DB::table('outflows')
            ->where('destination_bank_account_id', $this->account->id)
            ->where('type', 'Transferência')
            ->whereNotNull('paid_at')
            ->select([
                'id', 'description', DB::raw("'Transferência' as counterparty"), DB::raw('(amount - COALESCE(tax_amount, 0)) as amount'), 'paid_at as date',
                DB::raw("'transfer' as type"), DB::raw("'plus' as icon"), DB::raw("'info' as color"),
            ])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->where('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('paid_at', '<=', $this->dateTo . ' 23:59:59'));

        return $query1->unionAll($query2)
            ->unionAll($query3)
            ->unionAll($query4)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($this->perPage)
            ->get();
    }

    public function getConsolidatedBalanceProperty(): float
    {
        $limitDate = $this->dateTo ? ($this->dateTo . ' 23:59:59') : now()->endOfDay()->toDateTimeString();

        $rev = DB::table('revenues')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum('gross_amount');

        $exp = DB::table('expenditures')
            ->where('bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum('amount');

        $out = DB::table('outflows')
            ->where('origin_bank_account_id', $this->account->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));

        $trans = DB::table('outflows')
            ->where('destination_bank_account_id', $this->account->id)
            ->where('type', 'Transferência')
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('amount - COALESCE(tax_amount, 0)'));

        return (float) (($this->account->opening_balance + $rev + $trans) - ($exp + $out));
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.bank-accounts.statement', [
            'transactions' => $this->transactions,
        ]);
    }
}
