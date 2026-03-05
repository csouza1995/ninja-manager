<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Enums\ServiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Service;
use Carbon\Carbon;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class InvoiceForm extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public ?int $invoiceId = null;

    #[Validate('nullable|string')]
    public $number;

    #[Validate]
    public $access_key;

    #[Validate('required|numeric|min:0')]
    public $amount;

    #[Validate('required|date')]
    public $issued_at;

    #[Validate('nullable|exists:services,id')]
    public $service_id;

    #[Validate('nullable|exists:clients,id')]
    public $client_id;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|string')]
    public $ctn;

    #[Validate('nullable|string')]
    public $ctm;

    #[Validate('nullable|date')]
    public $competence_date;

    #[Validate('nullable|numeric|min:0')]
    public $tax_rate;

    #[Validate('nullable|string')]
    public $notes;

    public $xml_file;

    public $pdf_file;

    public bool $readOnly = false;

    public function mount()
    {
        $this->issued_at = Carbon::now()->format('Y-m-d');
    }

    public function rules()
    {
        return [
            'access_key' => 'nullable|string|unique:invoices,access_key,'.$this->invoiceId,
        ];
    }

    #[On('open-invoice-form')]
    public function open(?int $id = null, bool $readOnly = false)
    {
        $this->resetForm();
        $this->isOpen = true;
        $this->readOnly = $readOnly;

        if ($id) {
            $this->loadInvoice($id);
        }
    }

    public function removeMedia(int $mediaId)
    {
        if ($this->readOnly) {
            return;
        }

        $invoice = Invoice::find($this->invoiceId);
        if ($invoice) {
            $media = $invoice->media()->find($mediaId);
            if ($media) {
                $media->delete();
                session()->flash('success', 'Arquivo removido com sucesso!');
            }
        }
    }

    public function loadInvoice(int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceId = $id;
        $this->number = $invoice->number;
        $this->access_key = $invoice->access_key;
        $this->amount = $invoice->amount;
        $this->issued_at = $invoice->issued_at ? $invoice->issued_at->format('Y-m-d\TH:i') : null;
        $this->competence_date = $invoice->competence_date ? $invoice->competence_date->format('Y-m-d') : null;
        $this->tax_rate = $invoice->tax_rate;
        $this->client_id = $invoice->client_id;
        $this->service_id = $invoice->service_id;
        $this->description = $invoice->description;
        $this->ctn = $invoice->ctn;
        $this->ctm = $invoice->ctm;
        $this->notes = $invoice->notes;
    }

    public function save()
    {
        $this->validate();

        $oldInvoice = $this->invoiceId ? Invoice::find($this->invoiceId) : null;
        $oldServiceId = $oldInvoice ? $oldInvoice->service_id : null;

        $invoice = Invoice::updateOrCreate(
            ['id' => $this->invoiceId],
            [
                'number' => $this->number,
                'access_key' => $this->access_key,
                'amount' => $this->amount,
                'issued_at' => $this->issued_at,
                'competence_date' => $this->competence_date,
                'tax_rate' => $this->tax_rate,
                'client_id' => $this->client_id,
                'service_id' => $this->service_id,
                'description' => $this->description,
                'ctn' => $this->ctn,
                'ctm' => $this->ctm,
                'notes' => $this->notes,
            ]
        );

        if ($this->xml_file) {
            $invoice->addMedia($this->xml_file->getRealPath())
                ->usingName('XML NFSe '.$invoice->number)
                ->usingFileName('nfse_xml_'.$invoice->number.'.xml')
                ->toMediaCollection('xml_files');
        }

        if ($this->pdf_file) {
            $invoice->addMedia($this->pdf_file->getRealPath())
                ->usingName('PDF NFSe '.$invoice->number)
                ->usingFileName('nfse_pdf_'.$invoice->number.'.pdf')
                ->toMediaCollection('pdf_files');
        }

        // Automations: Unlink old service if changed
        if ($oldServiceId && $oldServiceId !== $this->service_id) {
            $oldService = Service::find($oldServiceId);
            if ($oldService) {
                $oldService->update(['is_invoiced' => false]);
                if ($oldService->revenue && $oldService->revenue->invoice_id === $invoice->id) {
                    $oldService->revenue->update(['invoice_id' => null]);
                }
            }
        }

        // Automations: Link new service
        if ($this->service_id) {
            $service = Service::find($this->service_id);
            if ($service) {
                $service->markAsInvoiced();

                if ($service->revenue) {
                    $service->revenue->update(['invoice_id' => $invoice->id]);
                }
            }
        }

        $this->dispatch('invoice-saved');
        session()->flash('success', 'Nota Fiscal salva com sucesso!');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function updatedClientId()
    {
        $this->service_id = null;
    }

    public function updatedServiceId()
    {
        if ($this->service_id && empty($this->amount)) {
            $service = Service::find($this->service_id);
            if ($service && $service->total) {
                $this->amount = $service->total;
            }
        }
    }

    private function resetForm()
    {
        $this->reset([
            'invoiceId', 'number', 'access_key', 'amount', 'service_id', 'client_id',
            'description', 'ctn', 'ctm', 'competence_date', 'tax_rate', 'notes',
            'xml_file', 'pdf_file',
        ]);
        $this->issued_at = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function updatedXmlFile()
    {
        if (! $this->xml_file) {
            return;
        }

        try {
            $xmlString = file_get_contents($this->xml_file->getRealPath());
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                session()->flash('error', 'Falha ao ler o arquivo XML.');

                return;
            }

            // Register namespaces if necessary based on NFSe format, or direct access.
            // Using direct access based on provided generic structure:

            if (isset($xml->infNFSe)) {
                $inf = $xml->infNFSe;

                // 1. Chave de Acesso (remove letras)
                if (isset($inf['Id'])) {
                    $this->access_key = preg_replace('/[^0-9]/', '', (string) $inf['Id']);
                }

                // 2. Número (Pad to 6 digits)
                if (isset($inf->nNFSe)) {
                    $this->number = str_pad((string) $inf->nNFSe, 6, '0', STR_PAD_LEFT);
                }

                if (isset($inf->DPS->infDPS)) {
                    $dps = $inf->DPS->infDPS;

                    // 3. Emissão
                    if (isset($dps->dhEmi)) {
                        $this->issued_at = Carbon::parse((string) $dps->dhEmi)->format('Y-m-d\TH:i');
                    }

                    // 4. Competência
                    if (isset($inf->dhProc)) {
                        $this->competence_date = Carbon::parse((string) $inf->dhProc)->format('Y-m-d');
                    }

                    // 5. Valores & Tributos
                    if (isset($dps->valores->vServPrest->vServ)) {
                        $this->amount = (float) $dps->valores->vServPrest->vServ;
                    }

                    if (isset($dps->valores->trib->totTrib->pTotTribSN)) {
                        $this->tax_rate = (float) $dps->valores->trib->totTrib->pTotTribSN;
                    }

                    // 6. Tomador / Cliente (Movemos para cima para garantir que os serviços filtrem corretamente)
                    if (isset($dps->toma)) {
                        $rawDocument = (string) $dps->toma->CNPJ ?? (string) $dps->toma->CPF;
                        $cleanDocument = preg_replace('/[^0-9]/', '', $rawDocument);
                        $name = (string) $dps->toma->xNome;

                        if ($cleanDocument && $name) {
                            // Find existing client where raw document stripped matches cleanDocument
                            $client = Client::whereRaw("REPLACE(REPLACE(REPLACE(document, '.', ''), '-', ''), '/', '') = ?", [$cleanDocument])->first();

                            if (! $client) {
                                // Create new
                                $client = Client::create([
                                    'document' => $cleanDocument,
                                    'name' => $name,
                                    'type' => strlen($cleanDocument) > 11 ? 'PJ' : 'PF',
                                ]);
                            }

                            $this->client_id = $client->id;
                            // Resetamos os serviços *apenas* se o usuário mudar na interface,
                            // aqui estamos carregando do XML então não chamamos updatedClientId() diretamente.
                        }
                    }

                    // 7. Serviço, Descrição e CTN
                    if (isset($dps->serv->cServ)) {
                        $this->ctn = (string) $dps->serv->cServ->cTribNac;
                        $this->description = (string) $dps->serv->cServ->xDescServ;

                        $foundId = null;

                        // Tentar achar ID do Serviço via Regex Básico
                        if (preg_match('/Servi[çc]o\s*#0*(\d+)/i', $this->description, $matches)) {
                            $foundId = (int) $matches[1];
                        } else {
                            // Se falhou no regex padrão, pergunte a IA!
                            try {
                                $prompt = "You are an AI that extracts the Ninja Manager Service ID from a Brazilian NFSe invoice description. The Service ID is an integer. Look for keywords like 'Serviço', 'Referente ao serviço', numbers with hash tags, or similar contextual cues pointing to a Service/Task number. Extract and reply ONLY with the numeric integer ID. If you cannot find any reference to a service number, reply ONLY with the explicit word 'null'.\n\nDescription: ".$this->description;

                                $response = Gemini::generativeModel('gemini-3-flash-preview')->generateContent($prompt);

                                $aiResult = trim($response->text() ?? 'null');
                                if ($aiResult !== 'null' && is_numeric($aiResult)) {
                                    $foundId = (int) $aiResult;
                                }
                            } catch (\Exception $e) {
                                // Fallback silently
                                Log::warning('Gemini Service ID Extraction failed: '.$e->getMessage());
                            }
                        }

                        if ($foundId && Service::where('id', $foundId)->exists()) {
                            $this->service_id = $foundId;
                            $this->updatedServiceId();
                        }
                    }
                }
            }

            session()->flash('success', 'XML importado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao processar XML: '.$e->getMessage());
        }
    }

    private function getServiceList(): Collection
    {
        $services = collect();

        if ($this->client_id) {
            $services = Service::with('role')
                ->where('client_id', $this->client_id)
                ->whereIn('status', [
                    ServiceStatus::Finalized,
                    ServiceStatus::InProgress,
                    ServiceStatus::Delivered,
                ])
                ->orderBy('id', 'desc')
                ->get();
        }

        return $services;
    }

    public function render()
    {
        return view('livewire.components.financial.invoice-form', [
            'services' => $this->getServiceList(),
            'clients' => Client::orderBy('name')->get(),
        ]);
    }
}
