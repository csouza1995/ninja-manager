<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="ninja-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Ninja Manager' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="min-h-screen bg-base-100 font-sans antialiased">
    <div class="navbar bg-base-200 border-b border-base-300 px-4 mb-8">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                </div>
                <ul tabindex="0"
                    class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-200 rounded-box w-52">
                    <li><a href="/" wire:navigate>Dashboard</a></li>

                    <li class="menu-title">Gestão</li>
                    <li><a href="{{ route('services.index') }}" wire:navigate>Serviços</a></li>
                    <li><a href="{{ route('receipts.index') }}" wire:navigate>Recibos</a></li>
                    <li><a href="{{ route('clients.index') }}" wire:navigate>Clientes</a></li>

                    <li class="menu-title">Financeiro</li>
                    <li><a href="{{ route('financial.dashboard') }}" wire:navigate>Painel Geral</a></li>
                    <li><a href="{{ route('financial.revenues') }}" wire:navigate>Receitas</a></li>
                    <li><a href="{{ route('financial.expenditures') }}" wire:navigate>Despesas</a></li>
                    <li><a href="{{ route('financial.invoices') }}" wire:navigate>Notas Fiscais (NF)</a></li>
                    <li><a href="{{ route('financial.outflows') }}" wire:navigate>Saídas & Retiradas</a></li>

                    <li class="menu-title">Configurações</li>
                    <li><a href="{{ route('executors.index') }}" wire:navigate>Executantes</a></li>
                    <li><a href="{{ route('roles.index') }}" wire:navigate>Funções</a></li>
                    <li><a href="{{ route('service-items.index') }}" wire:navigate>Itens de Serviço</a></li>
                    <li><a href="{{ route('financial.bank-accounts') }}" wire:navigate>Contas Bancárias</a></li>
                    <li><a href="{{ route('financial.taxes') }}" wire:navigate>Impostos</a></li>
                    <li><a href="{{ route('settings.database') }}">Banco de Dados</a></li>
                </ul>
            </div>
            <a href="/" wire:navigate class="flex items-center gap-2">
                <img src="/images/logo.png" alt="Ninja Manager" class="h-10 w-auto">
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li><a href="/" wire:navigate @class(['active' => request()->is('/')])>Dashboard</a></li>

                <li class="dropdown dropdown-hover dropdown-bottom">
                    <div tabindex="0" role="button" @class([
                        'active' =>
                            request()->is('services*') ||
                            request()->is('receipts*') ||
                            request()->is('clients*'),
                    ])
                        class="flex items-center gap-1 cursor-pointer">
                        Gestão
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 opacity-50">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content z-[2] menu p-2 shadow-xl bg-base-200 border border-base-300 rounded-box w-52 top-full mt-0">
                        <li><a href="{{ route('services.index') }}" wire:navigate>Serviços</a></li>
                        <li><a href="{{ route('receipts.index') }}" wire:navigate>Recibos</a></li>
                        <li><a href="{{ route('clients.index') }}" wire:navigate>Clientes</a></li>
                    </ul>
                </li>

                <li class="dropdown dropdown-hover dropdown-bottom">
                    <div tabindex="0" role="button" @class([
                        'active' =>
                            request()->is('financial*') &&
                            !request()->is('financial/bank-accounts*') &&
                            !request()->is('financial/taxes*'),
                    ])
                        class="flex items-center gap-1 cursor-pointer">
                        Financeiro
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 opacity-50">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content z-[2] menu p-2 shadow-xl bg-base-200 border border-base-300 rounded-box w-52 top-full mt-0">
                        <li><a href="{{ route('financial.dashboard') }}" wire:navigate>Painel Geral</a></li>
                        <li><a href="{{ route('financial.revenues') }}" wire:navigate>Receitas</a></li>
                        <li><a href="{{ route('financial.expenditures') }}" wire:navigate>Despesas</a></li>
                        <li><a href="{{ route('financial.invoices') }}" wire:navigate>Notas Fiscais (NF)</a></li>
                        <li><a href="{{ route('financial.outflows') }}" wire:navigate>Saídas & Retiradas</a></li>
                    </ul>
                </li>

                <li class="dropdown dropdown-hover dropdown-bottom dropdown-end">
                    <div tabindex="0" role="button" @class([
                        'active' =>
                            request()->is('executors*') ||
                            request()->is('roles*') ||
                            request()->is('service-items*') ||
                            request()->is('financial/bank-accounts*') ||
                            request()->is('financial/taxes*') ||
                            request()->is('settings*'),
                    ])
                        class="flex items-center gap-1 cursor-pointer">
                        Configurações
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 opacity-50">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content z-[2] menu p-2 shadow-xl bg-base-200 border border-base-300 rounded-box w-64 top-full mt-0">
                        <li><a href="{{ route('executors.index') }}" wire:navigate>Executantes</a></li>
                        <li><a href="{{ route('roles.index') }}" wire:navigate>Funções</a></li>
                        <li><a href="{{ route('service-items.index') }}" wire:navigate>Itens de Serviço</a></li>
                        <li><a href="{{ route('financial.bank-accounts') }}" wire:navigate>Contas Bancárias</a></li>
                        <li><a href="{{ route('financial.taxes') }}" wire:navigate>Impostos</a></li>
                        <li><a href="{{ route('settings.database') }}">Banco de Dados</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="navbar-end gap-2">
            <button class="btn btn-ghost btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </div>

    <main class="container mx-auto px-4 pb-24">
        {{ $slot }}
    </main>

    <footer
        class="footer items-center p-4 bg-base-200/80 backdrop-blur text-base-content border-t border-base-300 fixed bottom-0 w-full z-10">
        <aside class="items-center grid-flow-col">
            <img src="/images/logo.png" alt="Ninja Manager" class="h-6 w-auto grayscale opacity-30 mr-2">
            <p>© {{ date('Y') }} - Ninja Manager - O Futuro da Impressão 3D</p>
        </aside>
    </footer>
</body>

</html>
