<?php

namespace App\Http\Controllers;

use App\Models\Receipt;

class ReceiptController extends Controller
{
    /**
     * Display the printable receipt view.
     */
    public function print(Receipt $receipt)
    {
        if ($receipt->is_signed && $receipt->hasSignedPdf()) {
            return redirect($receipt->getSignedPdfUrl());
        }

        $receipt->load(['service.client', 'service.executor', 'service.role', 'service.items']);

        return view('receipts.template', [
            'receipt' => $receipt,
            'service' => $receipt->service,
        ]);
    }
}
