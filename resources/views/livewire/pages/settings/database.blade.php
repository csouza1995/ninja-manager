<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-primary mb-8 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
            </svg>
            Banco de Dados
        </h1>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Export Card -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-primary italic">Exportar Dados</h2>
                    <p class="text-sm text-base-content/70">Baixe um arquivo JSON contendo todos os seus dados ninja
                        (Clientes e Serviços).</p>

                    <div class="mt-4 p-4 bg-base-200 rounded-lg text-xs font-mono opacity-80">
                        { "version": "1.0", "data": { ... } }
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <button wire:click="export" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download JSON
                        </button>
                    </div>
                </div>
            </div>

            <!-- Import Card -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-secondary italic">Importar Dados</h2>
                    <p class="text-sm text-base-content/70">Restaure seu banco de dados a partir de um arquivo anterior.
                    </p>

                    <div class="form-control w-full mt-4">
                        <label class="label">
                            <span class="label-text">Arquivo JSON</span>
                        </label>
                        <input type="file" wire:model="jsonFile"
                            class="file-input file-input-bordered file-input-secondary w-full" />
                        <div wire:loading wire:target="jsonFile" class="text-xs text-secondary mt-1 italic">
                            Processando...</div>
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <button wire:click="import" wire:loading.attr="disabled" class="btn btn-secondary px-8">
                            <span wire:loading wire:target="import" class="loading loading-spinner"></span>
                            Restaurar do Arquivo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($importStats)
            <div class="mt-8 card bg-base-200 shadow-xl border border-base-300">
                <div class="card-body">
                    <h3 class="font-bold text-lg mb-4 italic text-secondary border-b border-base-300 pb-2">Resumo da
                        Missão:</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-base-100 p-4 rounded-xl shadow-sm">
                            <div class="text-[10px] font-bold uppercase opacity-40">Clientes</div>
                            <div class="text-2xl font-black text-secondary">{{ $importStats['clients'] }}</div>
                        </div>
                        <div class="bg-base-100 p-4 rounded-xl shadow-sm">
                            <div class="text-[10px] font-bold uppercase opacity-40">Serviços</div>
                            <div class="text-2xl font-black text-secondary">{{ $importStats['services'] }}</div>
                        </div>
                        <div class="bg-base-100 p-4 rounded-xl shadow-sm border-l-4 border-primary">
                            <div class="text-[10px] font-bold uppercase text-primary/60">Itens Auto-Reg.</div>
                            <div class="text-2xl font-black text-primary">{{ $importStats['items_registered'] }}</div>
                        </div>
                        <div class="bg-base-100 p-4 rounded-xl shadow-sm">
                            <div class="text-[10px] font-bold uppercase opacity-40">Funções/Exec.</div>
                            <div class="text-2xl font-black text-secondary">
                                {{ $importStats['roles'] + $importStats['executors'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-12 p-6 bg-warning/5 border border-warning/20 rounded-2xl flex gap-4 items-start italic">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 text-warning shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 18.75a6 6 0 0 0 6-6c0-3.416-2.841-5.672-4.735-6.848a.75.75 0 0 0-.847 1.25c1.474.914 3.082 2.739 3.082 5.598a4.5 4.5 0 1 1-9 0c0-2.859 1.608-4.684 3.082-5.598a.75.75 0 1 0-.847-1.25C6.841 6.078 4 8.334 4 11.75a6 6 0 0 0 6 6M10.5 21a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Z" />
            </svg>
            <div class="text-sm text-base-content/70 leading-relaxed">
                <p class="font-bold text-warning mb-1">Dica Ninja:</p>
                A importação usa o **Documento/Código** para evitar duplicidade. Serviços novos são sempre adicionados
                preservando seu histórico.
            </div>
        </div>
    </div>
</div>
