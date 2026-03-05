<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl border border-primary/20">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-primary">
                    @if ($readOnly)
                        Visualizar Nota Fiscal
                    @else
                        {{ $invoiceId ? 'Editar Nota Fiscal' : 'Registrar Nova Nota Fiscal' }}
                    @endif
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Uploads -->
                    @php $invoice = $invoiceId ? \App\Models\Invoice::find($invoiceId) : null; @endphp
                    <div class="form-control md:col-span-1 bg-base-200/50 p-4 rounded-lg border border-base-300">
                        <label class="label"><span class="label-text font-bold">XML (NFSe)</span></label>
                        @if ($invoice && $invoice->hasMedia('xml_files'))
                            <div
                                class="flex items-center justify-between bg-base-100 p-2 rounded-lg border border-base-300">
                                <a href="{{ $invoice->getFirstMediaUrl('xml_files') }}" target="_blank"
                                    class="btn btn-sm btn-info btn-outline flex-1 mr-2">
                                    Visualizar XML
                                </a>
                                @if (!$readOnly)
                                    <button wire:click="removeMedia({{ $invoice->getFirstMedia('xml_files')->id }})"
                                        class="btn btn-sm btn-square btn-error btn-outline" title="Remover Arquivo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @else
                            <input type="file" wire:model.live="xml_file" accept=".xml"
                                class="file-input file-input-bordered file-input-primary w-full"
                                @disabled($readOnly) />
                            <div wire:loading wire:target="xml_file" class="text-sm text-info mt-2">Processando XML...
                            </div>
                            @error('xml_file')
                                <span class="text-error text-xs p-1">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                    <div class="form-control md:col-span-1 bg-base-200/50 p-4 rounded-lg border border-base-300">
                        <label class="label"><span class="label-text font-bold">PDF (NFSe)</span></label>
                        @if ($invoice && $invoice->hasMedia('pdf_files'))
                            <div
                                class="flex items-center justify-between bg-base-100 p-2 rounded-lg border border-base-300">
                                <a href="{{ $invoice->getFirstMediaUrl('pdf_files') }}" target="_blank"
                                    class="btn btn-sm btn-error flex-1 mr-2">
                                    Visualizar PDF
                                </a>
                                @if (!$readOnly)
                                    <button wire:click="removeMedia({{ $invoice->getFirstMedia('pdf_files')->id }})"
                                        class="btn btn-sm btn-square btn-error btn-outline" title="Remover Arquivo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @else
                            <input type="file" wire:model.live="pdf_file" accept=".pdf"
                                class="file-input file-input-bordered file-input-secondary w-full"
                                @disabled($readOnly) />
                            <div wire:loading wire:target="pdf_file" class="text-sm text-info mt-2">Anexando...</div>
                            @error('pdf_file')
                                <span class="text-error text-xs p-1">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                    <!-- Row 1: Number, Competence, Issued -->
                    <div class="form-control">
                        <label class="label"><span class="label-text">Número da NF</span></label>
                        <input type="text" wire:model="number" class="input input-bordered w-full"
                            placeholder="Ex: 000123" @disabled($readOnly) />
                        @error('number')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Data Competência</span></label>
                        <input type="date" wire:model="competence_date" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('competence_date')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Data de Emissão</span></label>
                        <input type="datetime-local" wire:model="issued_at" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('issued_at')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Cliente / Tomador</span></label>
                        <select wire:model.live="client_id" class="select select-bordered w-full"
                            @disabled($readOnly)>
                            <option value="">Selecione um Cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Row 2: Service and Amount -->
                    <div class="form-control">
                        <label class="label"><span class="label-text">Vincular Serviço</span></label>
                        <select wire:model.live="service_id" class="select select-bordered w-full"
                            @disabled(!$client_id || $readOnly)>
                            <option value="">
                                {{ !$client_id ? 'Selecione um cliente primeiro' : 'NF Avulsa (Sem vínculo)' }}
                            </option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected($service->id == $service_id)>
                                    #{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}
                                    -
                                    ({{ \Illuminate\Support\Str::limit($service->role->name ?? 'Serviço', 20) }})
                                    - R$
                                    {{ number_format($service->total, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">Valor da Nota</span></label>
                        <div class="join w-full">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model="amount"
                                class="input input-bordered w-full join-item" placeholder="0.00"
                                @disabled($readOnly) />
                        </div>
                        @error('amount')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Row 3: Tax Rate (25%), Access Key (75%) -->
                    <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row gap-4">
                        <div class="form-control w-full md:w-1/4">
                            <label class="label"><span class="label-text">Taxa Tributos (%)</span></label>
                            <input type="number" step="0.01" wire:model="tax_rate"
                                class="input input-bordered w-full" @disabled($readOnly) />
                            @error('tax_rate')
                                <span class="text-error text-xs p-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full md:w-3/4">
                            <label class="label"><span class="label-text">Chave de Acesso (44 dígitos)</span></label>
                            <input type="text" wire:model="access_key"
                                class="input input-bordered w-full font-mono text-sm"
                                placeholder="Chave de acesso da NF-e" @disabled($readOnly) />
                            @error('access_key')
                                <span class="text-error text-xs p-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 4: CTN, CTM -->
                    <div class="form-control">
                        <label class="label"><span class="label-text">CTN (Cód. Trib. Nac)</span></label>
                        <input type="text" wire:model="ctn" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('ctn')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">CTM (Cód. Trib. Mun)</span></label>
                        <input type="text" wire:model="ctm" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('ctm')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Row 5: Description, Notes -->
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Descrição dos Serviços</span></label>
                        <textarea wire:model="description" class="textarea textarea-bordered h-20 w-full"
                            placeholder="Descrição contida na nota..." @disabled($readOnly)></textarea>
                        @error('description')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Observações</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered h-20 w-full" placeholder="Detalhes adicionais..."
                            @disabled($readOnly)></textarea>
                        @error('notes')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn btn-ghost">
                        {{ $readOnly ? 'Fechar' : 'Cancelar' }}
                    </button>
                    @if (!$readOnly)
                        <button wire:click="save" class="btn btn-primary px-8">
                            {{ $invoiceId ? 'Atualizar NF' : 'Salvar NF' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
