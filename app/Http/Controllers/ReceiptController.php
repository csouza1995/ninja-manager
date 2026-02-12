<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Display the printable receipt view.
     */
    public function print(Receipt $receipt)
    {
        $receipt->load(['service.client', 'service.executor', 'service.role', 'service.items']);
        
        return view('receipts.template', [
            'receipt' => $receipt,
            'service' => $receipt->service,
        ]);
    }
}
