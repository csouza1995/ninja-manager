<?php

namespace App\Livewire\Pages\Settings;

use App\Services\SystemBackupService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Database extends Component
{
    use WithFileUploads;

    public $zipFile;

    public $importStats = null;

    public function exportFull(SystemBackupService $backupService)
    {
        try {
            $zipPath = $backupService->createBackup();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao gerar backup completo: '.$e->getMessage());
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
