<div>
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-primary">Dashboard</h2>
        <p class="mt-1 text-sm text-base-content/60">Visão geral do sistema</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5 mb-8">
        <!-- Negotiating -->
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div class="stat-title font-medium">Negociando</div>
                <div class="stat-value text-warning">{{ $stats['negotiating'] }}</div>
                <div class="stat-desc">Aguardando aprovação</div>
            </div>
        </div>

        <!-- Approved -->
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title font-medium">Aprovados</div>
                <div class="stat-value text-secondary">{{ $stats['approved'] }}</div>
                <div class="stat-desc">Acordo fechado</div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-info">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="stat-title font-medium">Em Andamento</div>
                <div class="stat-value text-info">{{ $stats['in_progress'] }}</div>
                <div class="stat-desc">Sendo executados</div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="stat-title font-medium">Entregues</div>
                <div class="stat-value text-primary">{{ $stats['delivered'] }}</div>
                <div class="stat-desc">Aguardando finalização</div>
            </div>
        </div>

        <!-- Finalized -->
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-success">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title font-medium">Finalizados</div>
                <div class="stat-value text-success">{{ $stats['finalized_period'] }}</div>
                <div class="stat-desc">Últimos 6 meses</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Services (Main) -->
        <div class="lg:col-span-2">
            <div class="card bg-base-200 border border-base-300 shadow-xl overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-6 pb-0 flex justify-between items-center">
                        <h3 class="card-title text-primary">Serviços Recentes</h3>
                        <a href="{{ route('services.index') }}" wire:navigate class="btn btn-ghost btn-sm">Ver todos</a>
                    </div>

                    @if ($recentServices->count() > 0)
                        <div class="overflow-x-auto mt-4">
                            <table class="table w-full">
                                <thead class="bg-base-300/50">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentServices as $service)
                                        @php
                                            $statusClasses = [
                                                'negotiating' => 'badge-warning text-warning-content',
                                                'approved' => 'badge-secondary',
                                                'cancelled' => 'badge-error',
                                                'in_progress' => 'badge-info',
                                                'delivered' => 'badge-primary',
                                                'finalized' => 'badge-success',
                                            ];
                                            $statusLabels = [
                                                'negotiating' => 'Negociando',
                                                'approved' => 'Aprovado',
                                                'cancelled' => 'Cancelado',
                                                'in_progress' => 'Em andamento',
                                                'delivered' => 'Entregue',
                                                'finalized' => 'Finalizado',
                                            ];
                                        @endphp
                                        <tr class="hover">
                                            <td class="font-mono text-xs opacity-60">
                                                #{{ str_pad($service->id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <div class="font-medium">{{ $service->client->name }}</div>
                                                <div class="text-[10px] opacity-50">{{ $service->role->name }}</div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-sm font-bold {{ $statusClasses[$service->status] ?? '' }}">
                                                    {{ $statusLabels[$service->status] ?? $service->status }}
                                                </span>
                                            </td>
                                            <td class="text-xs opacity-60">
                                                {{ $service->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center text-base-content/40 italic">
                            Nenhum serviço registrado recentemente.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Clients (Secondary) -->
        <div class="space-y-6">
            <div class="card bg-base-200 border border-base-300 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title text-sm uppercase tracking-wider opacity-60">Novos Clientes</h3>
                        <a href="{{ route('clients.index') }}" wire:navigate class="btn btn-ghost btn-xs">Ver todos</a>
                    </div>

                    @if ($recentClients->count() > 0)
                        <div class="space-y-4">
                            @foreach ($recentClients as $client)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold truncate">
                                            {{ $client->nickname ?: $client->name }}
                                        </div>
                                        @if ($client->nickname && $client->nickname !== $client->name)
                                            <div class="text-[10px] opacity-50 truncate">{{ $client->name }}</div>
                                        @endif
                                        <div class="text-[10px] opacity-60 font-mono">
                                            {{ $client->document }}
                                        </div>
                                        <div class="text-[10px] opacity-30 italic">
                                            {{ $client->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <span
                                        class="badge badge-xs {{ $client->type === 'individual' ? 'badge-success' : 'badge-ghost' }}">
                                        {{ $client->type === 'individual' ? 'PF' : 'PJ' }}
                                    </span>
                                </div>
                                @if (!$loop->last)
                                    <div class="divider my-0 opacity-10"></div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-xs opacity-40">
                            Sem novos clientes.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Registration Summary Card -->
            <div class="card bg-base-200 border border-base-300 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-sm uppercase tracking-wider opacity-60">Resumo de Cadastros</h3>
                    <div class="grid grid-cols-1 gap-4 mt-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="opacity-70">Total de Clientes</span>
                            <span class="font-bold">{{ $stats['total_clients'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="opacity-70">Executantes</span>
                            <span class="font-bold">{{ $stats['total_executors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="opacity-70">Itens de Serviço</span>
                            <span class="font-bold">{{ $stats['total_items'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
