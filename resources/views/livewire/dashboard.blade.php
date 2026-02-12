<div>
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-primary">Dashboard</h2>
        <p class="mt-1 text-sm text-base-content/60">Visão geral do sistema</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="stat-title">Executantes</div>
                <div class="stat-value text-primary">{{ $stats['executors'] }}</div>
            </div>
        </div>

        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="stat-title">Funções</div>
                <div class="stat-value text-primary">{{ $stats['roles'] }}</div>
            </div>
        </div>

        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="stat-title">Itens de Serviço</div>
                <div class="stat-value text-primary">{{ $stats['service_items'] }}</div>
            </div>
        </div>

        <div class="stats shadow bg-base-200 border border-base-300">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="stat-title">Clientes</div>
                <div class="stat-value text-primary">{{ $stats['clients'] }}</div>
            </div>
        </div>
    </div>

    <!-- Recent Clients -->
    <div class="card bg-base-200 border border-base-300 shadow-lg">
        <div class="card-body">
            <h3 class="card-title text-primary mb-4">Clientes Recentes</h3>

            @if ($recentClients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Documento</th>
                                <th>Cadastrado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentClients as $client)
                                <tr class="hover">
                                    <td>{{ $client->name }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $client->type === 'individual' ? 'badge-success' : 'badge-ghost' }}">
                                            {{ $client->type === 'individual' ? 'PF' : 'PJ' }}
                                        </span>
                                    </td>
                                    <td class="text-base-content/60">{{ $client->document }}</td>
                                    <td class="text-base-content/60">{{ $client->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-base-content/60">
                    Nenhum cliente cadastrado ainda.
                </div>
            @endif
        </div>
    </div>
</div>
