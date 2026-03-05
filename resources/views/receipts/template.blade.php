<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $receipt->receipt_number }}</title>
    <style>
        :root {
            --primary: #111;
            --accent: #ff0055;
            --text: #222;
            --muted: #888;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                background: white !important;
                padding: 0 !important;
            }

            .sidebar {
                background: #111 !important;
                color: white !important;
            }

            .no-print {
                display: none !important;
            }

            .receipt-page {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: var(--text);
            background: #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
            min-height: 100vh;
        }

        .receipt-page {
            width: 210mm;
            height: 297mm;
            display: flex;
            background: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            position: relative;
            margin: 0 auto;
        }

        /* Float Print Button */
        .print-controls {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }

        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-print:hover {
            transform: scale(1.05);
            background: #333;
        }

        /* Sidebar Branding */
        .sidebar {
            width: 50px;
            flex-shrink: 0;
            background: var(--primary);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 0;
            text-align: center;
        }

        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 12px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.1);
            margin: auto;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px 40px;
            width: 160mm;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .header .brand {
            display: flex;
            align-items: center;
        }

        .company-logo {
            height: 60px;
            width: auto;
            margin-right: 15px;
        }

        .company-name {
            font-size: 16px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .company-info {
            font-size: 10px;
            color: #777;
            line-height: 1.4;
        }

        .receipt-type {
            text-align: right;
        }

        .type-title {
            font-size: 24px;
            font-weight: 900;
            color: var(--primary);
        }

        .id-tag {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 10px;
            margin-top: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--primary);
        }

        /* Contractor Grid System (Flexbox) */
        .contractor-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        .field-group {
            display: flex;
            flex-direction: column;
        }

        .field-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 2px;
        }

        .field-value {
            font-size: 13px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
            display: block;
            width: 100%;
            min-height: 18px;
        }

        /* Items Section */
        .items-section {
            margin: 5px 0 15px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid #ddd;
            padding: 8px 5px;
        }

        .items-table td {
            padding: 8px 5px;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        .total-row td {
            padding-top: 15px;
            border-bottom: none;
            font-weight: 900;
        }

        .declaration-text {
            font-size: 13px;
            line-height: 1.6;
            color: #222;
            margin-bottom: 15px;
            text-align: justify;
        }

        .declaration-text strong {
            font-weight: 700;
            color: var(--primary);
        }

        /* Flex grow space logic */
        .spacer {
            flex-grow: 1;
        }

        .signatures-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 20px;
            padding-top: 20px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid var(--primary);
            margin-bottom: 8px;
            width: 100%;
        }

        .signature-info {
            font-size: 10px;
        }

        .signature-name {
            font-weight: 700;
            font-size: 12px;
        }

        .signature-role {
            color: var(--muted);
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        .footer {
            font-size: 10px;
            color: var(--muted);
            border-top: 1px solid #eee;
            padding-top: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Imprimir Recibo
        </button>
    </div>

    <div class="receipt-page">
        <aside class="sidebar">
            <div class="vertical-text">{{ strtoupper(config('company.name')) }}</div>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="brand">
                    <img src="{{ asset('storage/brand/logo.png') }}" alt="Logo" class="company-logo">
                    <div>
                        <div class="company-name">{{ config('company.name') }}</div>
                        <div class="company-info">
                            CNPJ {{ config('company.document') }}<br>
                            {{ config('company.address') }}<br>
                            {{ config('company.email') }}
                        </div>
                    </div>
                </div>
                <div class="receipt-type">
                    <div class="type-title">RECIBO</div>
                    <div class="id-tag">REF: {{ $receipt->receipt_number }}</div>
                </div>
            </header>

            <div class="section-title">Dados do Contratante</div>
            <div class="contractor-info">
                <!-- Linha 1: Nome e Documento -->
                <div class="row">
                    <div class="field-group" style="flex: 1;">
                        <span class="field-label">Cliente / Pagador</span>
                        <span class="field-value">{{ $service->client->name }}</span>
                    </div>
                    <div class="field-group" style="width: 180px;">
                        <span class="field-label">{{ $service->client->type === 'individual' ? 'CPF' : 'CNPJ' }}</span>
                        <span class="field-value">{{ $service->client->document }}</span>
                    </div>
                </div>
                <!-- Linha 2: Logradouro, Número, Bairro -->
                <div class="row">
                    <div class="field-group" style="flex: 3;">
                        <span class="field-label">Logradouro</span>
                        <span class="field-value">{{ $service->client->street }}</span>
                    </div>
                    <div class="field-group" style="width: 60px;">
                        <span class="field-label">Nº</span>
                        <span class="field-value">{{ $service->client->number }}</span>
                    </div>
                    <div class="field-group" style="flex: 1;">
                        <span class="field-label">Bairro</span>
                        <span class="field-value">{{ $service->client->neighborhood }}</span>
                    </div>
                </div>
                <!-- Linha 3: CEP, Complemento, Município / UF -->
                <div class="row">
                    <div class="field-group" style="width: 90px;">
                        <span class="field-label">CEP</span>
                        <span class="field-value">{{ $service->client->zip_code }}</span>
                    </div>
                    <div class="field-group" style="flex: 1;">
                        <span class="field-label">Complemento</span>
                        <span class="field-value">{{ $service->client->complement ?? '---' }}</span>
                    </div>
                    <div class="field-group" style="flex: 1.5;">
                        <span class="field-label">Município / UF</span>
                        <span class="field-value">{{ $service->client->city }} / {{ $service->client->state }}</span>
                    </div>
                </div>
            </div>

            <div class="section-title">Serviços Prestados</div>
            <section class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Descrição de Serviço</th>
                            <th style="text-align: center; width: 10%;">Qtd</th>
                            <th style="text-align: center; width: 15%;">Valor Unit.</th>
                            <th style="text-align: right; width: 15%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($service->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td style="text-align: center;">{{ number_format($item->quantity, 4, ',', '.') }}</td>
                                <td style="text-align: center;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                </td>
                                <td style="text-align: right; white-space: nowrap;">R$
                                    {{ number_format($item->total_price, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; text-transform: uppercase; font-size: 11px;">
                                Total Geral:</td>
                            <td style="text-align: right; font-size: 16px; color: var(--primary); white-space: nowrap;">
                                R$ {{ number_format($service->total, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <div class="section-title">Declaração do Executante</div>
            <p class="declaration-text">
                Declaro que os serviços descritos acima foram executados por
                <strong>{{ $service->executor->name }}</strong>,
                portador(a) do CPF nº <strong>{{ $service->executor->document }}</strong>,
                na função de <strong>{{ $service->role->name }}</strong>,
                @if ($service->started_at && $service->finished_at)
                    no período de
                    <strong>{{ $service->started_at->format('d/m/Y') }}</strong> até
                    <strong>{{ $service->finished_at->format('d/m/Y') }}</strong>,
                @else
                    na data de
                    <strong>{{ $service->created_at->format('d/m/Y') }}</strong>,
                @endif
                atuando em nome da <strong>{{ config('company.name') }}</strong> (Contratado), conforme especificado
                neste documento.
            </p>

            <div class="section-title">Nota de Compromisso</div>
            <p class="declaration-text" style="font-size: 12px;">
                Este comprovante tem caráter provisório e registra formalmente, perante os órgãos competentes,
                que o serviço acima descrito foi executado pelo <strong>Executante</strong>, atuando em nome do
                <strong>Contratado</strong>,
                e devidamente pago pelo <strong>Contratante</strong>, restando pendentes apenas os trâmites fiscais por
                parte do <strong>Contratado</strong>.
                Fica estabelecido que cabe ao <strong>Contratado</strong> fornecer o respectivo documento fiscal
                pendente — a Nota Fiscal eletrônica —
                assim que for concluído o processamento cadastral da <strong>{{ config('company.name') }}</strong>
                junto aos órgãos
                competentes.
            </p>

            <div class="spacer"></div>

            <div class="signatures-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-info">
                        <div class="signature-name">{{ $service->executor->name }}</div>
                        <div class="signature-role">Colaborador Executante</div>
                        <div class="signature-doc">CPF: {{ $service->executor->document }}</div>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-info">
                        <div class="signature-name">{{ config('company.owner_name') }}</div>
                        <div class="signature-role">Sócio Administrativo</div>
                        <div class="signature-doc">CPF: {{ config('company.owner_document') }}</div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                PIRACICABA/SP, {{ now()->format('d/m/Y') }}
            </footer>
        </main>
    </div>
</body>

</html>
