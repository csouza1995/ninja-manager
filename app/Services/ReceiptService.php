<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    /**
     * Generate or regenerate a receipt for a service.
     */
    public function generate(Service $service): Receipt
    {
        // Check if receipt already exists, otherwise create it
        $receipt = $service->receipt ?? Receipt::create(['service_id' => $service->id]);

        // Generate PDF
        $pdf = Pdf::loadView('receipts.template', [
            'service' => $service->load(['client', 'executor', 'role', 'items']),
            'receipt' => $receipt,
        ])->setPaper('a4', 'portrait');

        // Save PDF to temporary location
        $filename = "recibo_{$receipt->receipt_number_slug}.pdf";
        $tempPath = storage_path("app/public/temp/{$filename}");
        
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        $pdf->save($tempPath);

        // Add to Media Library
        $receipt->addMedia($tempPath)
            ->toMediaCollection('receipts');

        // Update service status flags if necessary
        $service->update(['is_documented' => true]);

        return $receipt;
    }

    /**
     * Delete a receipt and its associated media.
     */
    public function delete(Receipt $receipt): void
    {
        $receipt->clearMediaCollection('receipts');
        $receipt->delete();
    }
}
