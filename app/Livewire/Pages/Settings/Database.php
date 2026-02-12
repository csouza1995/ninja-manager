<?php

namespace App\Livewire\Pages\Settings;

use App\Services\DataSyncService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Database extends Component
{
    use WithFileUploads;

    public $jsonFile;
    public $importStats = null;

    public function export(DataSyncService $syncService)
    {
        $data = $syncService->export();
        $filename = 'backup_ninja_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename);
    }

    public function import(DataSyncService $syncService)
    {
        try {
            if ($this->jsonFile) {
                $content = file_get_contents($this->jsonFile->getRealPath());
                $data = json_decode($content, true);
            } else {
                session()->flash('error', 'Por favor, carregue um arquivo JSON.');
                return;
            }

            if (!$data) {
                session()->flash('error', 'Formato JSON inválido no arquivo.');
                return;
            }

            $this->importStats = $syncService->import($data);
            session()->flash('success', 'Importação concluída com sucesso!');
            
            $this->reset(['jsonFile']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro na importação: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.settings.database')
            ->layout('components.layouts.app');
    }
}
