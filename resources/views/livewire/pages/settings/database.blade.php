<div class="p-6">
    <div class="max-w-5xl mx-auto">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-black text-primary flex items-center gap-3 tracking-tight">
                    <div class="p-2 bg-primary/10 rounded-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                        </svg>
                    </div>
                    Configurações do Sistema
                </h1>
                <p class="mt-2 text-base-content/60 font-medium">Backup, restauração e integridade de dados do Ninja
                    Manager</p>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="space-y-4 mb-8">
            @if (session()->has('success'))
                <div class="alert alert-success shadow-lg border-none bg-success/20 text-success-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-error shadow-lg border-none bg-error/20 text-error-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        {{-- Content Area --}}
        <div class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Full Backup Card --}}
                <div class="card bg-base-100 shadow-2xl border border-primary/20 overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-48 h-48">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <div class="card-body relative z-10">
                        <h2 class="card-title text-2xl font-black text-primary mb-2 flex items-center gap-2">
                            Backup Ninja ZIP
                            <div class="badge badge-primary badge-sm">PROTEÇÃO TOTAL</div>
                        </h2>
                        <p class="text-base-content/70">
                            Gere um arquivo compactado (ZIP) contendo o banco de dados completo e todos os arquivos,
                            imagens e anexos da pasta <code class="bg-base-300 px-1 rounded">storage</code>.
                        </p>

                        <div class="mt-6 flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-sm font-bold opacity-60">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-success">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Banco de Dados (SQL/JSON v1.1)
                            </div>
                            <div class="flex items-center gap-2 text-sm font-bold opacity-60">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-success">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Arquivos & Anexos (Storage Public)
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-8">
                            <button wire:click="exportFull" wire:loading.attr="disabled"
                                class="btn btn-primary btn-lg shadow-xl hover:scale-105 active:scale-95 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Baixar Sistema Completo (.ZIP)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Full Restore Card --}}
                <div class="card bg-base-200/50 shadow-xl border border-base-300 border-dashed">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-bold flex items-center gap-2">
                            Restaurar Sistema
                        </h2>
                        <p class="text-sm text-base-content/60">
                            Carregue um backup ZIP anterior para restaurar todo o ambiente Ninja.
                        </p>

                        <div class="form-control w-full mt-6">
                            <div class="relative group">
                                <input type="file" wire:model="zipFile"
                                    class="file-input file-input-bordered file-input-primary w-full h-24 bg-base-100/50" />
                                <div
                                    class="absolute inset-x-0 bottom-2 text-center pointer-events-none opacity-40 text-[10px] font-bold uppercase tracking-widest">
                                    Arraste o arquivo .zip aqui
                                </div>
                            </div>
                            <div wire:loading wire:target="zipFile"
                                class="text-xs text-primary font-bold mt-2 animate-pulse flex items-center gap-2">
                                <span class="loading loading-spinner loading-xs font-bold"></span>
                                Processando arquivo ZIP...
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <button wire:click="importFull" wire:loading.attr="disabled"
                                wire:confirm="CUIDADO: Isso irá substituir os dados e arquivos atuais. Deseja prosseguir?"
                                class="btn btn-neutral px-8">
                                <span wire:loading wire:target="importFull" class="loading loading-spinner"></span>
                                Restaurar Backup Completo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Import Stats --}}
            @if ($importStats)
                <div
                    class="card bg-neutral text-neutral-content shadow-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-2xl uppercase tracking-tighter italic">Relatório Ninja</h3>
                            <div class="badge badge-success font-bold">SUCESSO</div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                            @foreach ([
        'Clientes' => $importStats['clients'],
        'Serviços' => $importStats['services'],
        'Horas Trab.' => $importStats['work_hours'] ?? 0,
        'Itens Reg.' => $importStats['items_registered'],
        'Equipe' => $importStats['roles'] + $importStats['executors'],
    ] as $label => $val)
                                <div class="bg-base-100/10 p-4 rounded-2xl border border-white/5 backdrop-blur-sm">
                                    <div class="text-[10px] font-black uppercase opacity-60 tracking-widest mb-1">
                                        {{ $label }}</div>
                                    <div class="text-4xl font-black text-white italic drop-shadow-lg">
                                        {{ $val }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Dica Ninja --}}
            <div
                class="p-8 bg-primary/5 rounded-3xl border border-primary/10 flex flex-col md:flex-row gap-6 items-center italic">
                <div class="p-4 bg-primary/10 rounded-full text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                </div>
                <div class="text-center md:text-left space-y-1">
                    <p class="text-xl font-black text-primary uppercase tracking-tight">Estratégia de Sincronização</p>
                    <p class="text-base-content/60 leading-relaxed font-medium">A restauração é inteligente: ela
                        identifica **Clientes** pelo documento e **Itens** pelo código. Serviços são preservados para
                        manter seu histórico de faturamento íntegro e imutável. No modo ZIP, todos os seus anexos também
                        retornam ao lugar de origem.</p>
                </div>
            </div>
        </div>
    </div>
</div>
