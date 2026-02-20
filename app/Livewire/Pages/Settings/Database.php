<?php

namespace App\Livewire\Pages\Settings;

use App\Services\DataSyncService;
use App\Services\SystemBackupService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Database extends Component
{
    use WithFileUploads;

    public $jsonFile;

    public $zipFile;

    public $importStats = null;

    public $activeTab = 'full'; // 'full' or 'partial'

    public function exportJson(DataSyncService $syncService)
    {
        $data = $syncService->export();
        $filename = 'backup_ninja_data_'.now()->format('Y-m-d_H-i-s').'.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename);
    }

    public function exportFull(SystemBackupService $backupService)
    {
        try {
            $zipPath = $backupService->createBackup();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao gerar backup completo: '.$e->getMessage());
        }
    }

    public function importJson(DataSyncService $syncService)
    {
        try {
            if (! $this->jsonFile) {
                session()->flash('error', 'Por favor, carregue um arquivo JSON.');

                return;
            }

            $content = file_get_contents($this->jsonFile->getRealPath());
            $data = json_decode($content, true);

            if (! $data) {
                session()->flash('error', 'Formato JSON inválido no arquivo.');

                return;
            }

            $this->importStats = $syncService->import($data);
            session()->flash('success', 'Importação parcial concluída com sucesso!');

            $this->reset(['jsonFile']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro na importação: '.$e->getMessage());
        }
    }

    public function importFull(SystemBackupService $backupService)
    {
        try {
            if (! $this->zipFile) {
                session()->flash('error', 'Por favor, carregue o arquivo ZIP do backup.');

                return;
            }

            $tempPath = $this->zipFile->getRealPath();
            $this->importStats = $backupService->restoreBackup($tempPath);

            session()->flash('success', 'Restauração completa do sistema realizada com sucesso!');
            $this->reset(['zipFile']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro na restauração completa: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.settings.database')
            ->layout('components.layouts.app');
    }
}
