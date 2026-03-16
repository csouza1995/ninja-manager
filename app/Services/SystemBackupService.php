<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SystemBackupService
{
    public function __construct(protected DataSyncService $syncService) {}

    /**
     * Create a ZIP backup of the database and storage files.
     *
     * @return string Path to the generated ZIP file.
     *
     * @throws Exception
     */
    public function createBackup(): string
    {
        $backupDir = storage_path('app/backups/temp');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $zipFilename = "ninja_system_backup_{$timestamp}.zip";
        $zipPath = storage_path("app/backups/{$zipFilename}");

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Could not create ZIP file at {$zipPath}");
        }

        // 1. Add Database JSON
        $data = $this->syncService->export();
        $zip->addFromString('database.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 2. Add Storage Files (public)
        $storagePublicPath = storage_path('app/public');
        if (File::exists($storagePublicPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePublicPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'storage/'.substr($filePath, strlen($storagePublicPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Restore a backup from a ZIP file.
     *
     * @return array Import statistics
     *
     * @throws Exception
     */
    public function restoreBackup(string $zipFilePath): array
    {
        if (! File::exists($zipFilePath)) {
            throw new Exception("Backup file not found at {$zipFilePath}");
        }

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) !== true) {
            throw new Exception("Could not open ZIP file at {$zipFilePath}");
        }

        $extractPath = storage_path('app/backups/extract_'.uniqid());
        if (! File::makeDirectory($extractPath, 0755, true)) {
            throw new Exception('Could not create extraction directory');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // 1. Restore Database
        $jsonPath = $extractPath.'/database.json';
        if (! File::exists($jsonPath)) {
            File::deleteDirectory($extractPath);
            throw new Exception('Invalid backup: database.json missing');
        }

        $data = json_decode(File::get($jsonPath), true);
        $stats = $this->syncService->import($data);

        // 2. Restore Storage Files
        $storageExtractedPath = $extractPath.'/storage';
        if (File::exists($storageExtractedPath)) {
            $storagePublicPath = storage_path('app/public');

            // Backup current public to temp before overwriting?
            // For now, let's just copy over.
            File::copyDirectory($storageExtractedPath, $storagePublicPath);
        }

        // Cleanup
        File::deleteDirectory($extractPath);

        return $stats;
    }
}
