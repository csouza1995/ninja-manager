# Financial Flows

Mapa de relacionamentos, eventos e fluxos dos modelos financeiros.

---

## Visão Geral — Dependências entre Modelos

```
Service ──────────────────────────────────────────────────────────┐
  │                                                               │
  ├──[invoice vinculado]──→ Invoice ──[HasMany]──→ Revenue        │
  │                           │                       │           │
  ├──[revenue direto]─────────────────────────→ Revenue           │
  │                                               │               │
  │                           BelongsTo BankAccount ◄────────────┤
  │                           BelongsTo Invoice                   │
  │                           MorphTo linkable ◄── Outflow        │
  │                                                               │
  ├──[recibo]──→ Receipt (auto-numerado no creating)             │
  │                                                               │
  └── flags sincronizadas inline: is_paid, is_invoiced, is_documented

Outflow ──→ BankAccount (origin + destination para Transferência)
  └──[MorphOne]──→ Expenditure (INSS — criada manualmente)

Expenditure ──→ BankAccount
  └──[MorphTo model]──→ Revenue | Outflow (referência de imposto)
```

---

## Observers e Eventos

**Não existem Observers nem Event/Listener classes.** Todos os efeitos colaterais acontecem inline nos métodos `save()` dos componentes Livewire. Os únicos hooks de modelo são:

- `Receipt::boot()` — hook `creating` para auto-numeração
- Livewire dispatches: `revenue-saved`, `expenditure-saved`, `outflow-saved`, `invoice-saved`

---

## Revenue (Recebimentos)

### Relacionamentos

| Tipo | Modelo | FK / Polimorfismo |
|---|---|---|
| `BelongsTo` | `BankAccount` | `bank_account_id` |
| `BelongsTo` | `Invoice` | `invoice_id` |
| `BelongsTo` | `Service` | `service_id` |
| `MorphTo` | qualquer model | `linkable_type` / `linkable_id` |

### Atributos Computados

```php
tax_amount  = gross_amount * (tax_percentage / 100)
net_amount  = (gross_amount + adjustment_amount) - tax_amount
```

### Campos Críticos

| Campo | Tipo | Descrição |
|---|---|---|
| `gross_amount` | decimal | Valor bruto recebido |
| `tax_percentage` | decimal | % de imposto sobre a receita |
| `adjustment_amount` | decimal | Ajuste (positivo ou negativo) |
| `adjustment_reason` | string | Motivo do ajuste |
| `invoice_id` | FK | Nota fiscal vinculada |
| `paid_at` | date | Data de pagamento (null = pendente) |
| `due_date` | date | Data de vencimento |
| `linkable_type` / `linkable_id` | morph | Origem vinculada (ex: Outflow) |

### O que acontece ao salvar (RevenueForm)

1. `Revenue::updateOrCreate()` com todos os campos
2. Se `service_id` preenchido → `Service.is_paid = !is_null(paid_at)` sincronizado imediatamente
3. Dispatch `revenue-saved`

> **Atenção:** Sem transação DB. Se o sync do Service falhar, a Revenue já foi salva.

---

## Invoice (Notas Fiscais)

### Relacionamentos

| Tipo | Modelo | FK |
|---|---|---|
| `HasMany` | `Revenue` | `invoice_id` |
| `BelongsTo` | `Client` | `client_id` (nullable) |
| `BelongsTo` | `Service` | `service_id` (nullable) |
| `HasMedia` (Spatie) | — | coleções `xml_files`, `pdf_files` |

### Campos Críticos

| Campo | Tipo | Descrição |
|---|---|---|
| `number` | string | Número da NF |
| `access_key` | string | Chave de acesso NFSe |
| `amount` | decimal | Valor bruto da NF |
| `tax_rate` | decimal | Alíquota (%) |
| `tax_amount` | decimal | Valor do imposto calculado |
| `net_amount` | decimal | Valor líquido |
| `issued_at` | datetime | Data de emissão |
| `competence_date` | date | Data de competência |
| `ctn` / `ctm` | string | Campos de controle da prefeitura |

### O que acontece ao salvar (InvoiceForm)

1. `Invoice::updateOrCreate()` com todos os campos
2. Upload XML → coleção `xml_files` (Spatie)
3. Upload PDF → coleção `pdf_files` (Spatie)
4. **Se o Service vinculado mudou:**
   - Old service → `is_invoiced = false`, desvincula `revenue.invoice_id = null`
   - New service → `is_invoiced = true`, `service.revenue.invoice_id = invoice.id`
5. Dispatch `invoice-saved`

---

## Expenditure (Despesas)

### Relacionamentos

| Tipo | Modelo | FK / Polimorfismo |
|---|---|---|
| `BelongsTo` | `BankAccount` | `bank_account_id` |
| `MorphTo` | `Revenue` \| `Outflow` | `model_type` / `model_id` |

### Campos Críticos

| Campo | Tipo | Descrição |
|---|---|---|
| `destination` | string | Destinatário do pagamento |
| `classification` | string | Categoria (ex: `Imposto`, `INSS`, `DAS`) |
| `amount` | decimal | Valor da despesa |
| `adjustment_amount` | decimal | Ajuste (positivo ou negativo) |
| `paid_at` | date | Data de pagamento (null = pendente) |
| `due_date` | date | Vencimento |
| `reference_date` | date | Competência/referência |
| `model_type` / `model_id` | morph | Imposto referente a qual registro |

### O que acontece ao salvar (ExpenditureForm)

1. `Expenditure::updateOrCreate()` — sem side effects extras

### Automação reativa ao selecionar `model_id`

Quando o usuário escolhe um model vinculado (antes do save), o form preenche automaticamente:

| Condição | `classification` | `destination` | `amount` |
|---|---|---|---|
| model = `Revenue` | `Imposto` | `Receita Federal` | `revenue.tax_amount` |
| model = `Outflow` | `Imposto S/ Prolabore` | `INSS / Receita Federal` | `outflow.tax_amount` |

Adicionalmente:
- `due_date` = `model.paid_at + 1 mês, dia 15`
- `description` = `"Imposto Ref. {description} {mm/YYYY} ({mm/YYYY})"`

> **Importante:** A Expenditure referente ao INSS de um Outflow **não é criada automaticamente** — o usuário cria manualmente via ExpenditureForm selecionando o Outflow como model.

### Filtro de Impostos no Dashboard

O Dashboard exclui despesas de imposto das despesas operacionais filtrando por `classification`:
```php
->where('classification', 'not like', '%Imposto%')
->where('classification', 'not like', '%INSS%')
->where('classification', 'not like', '%DAS%')
```

---

## Outflow (Retiradas / Pró-labore)

### Relacionamentos

| Tipo | Modelo | FK |
|---|---|---|
| `BelongsTo` | `BankAccount` | `origin_bank_account_id` |
| `BelongsTo` | `BankAccount` | `destination_bank_account_id` (Transferências) |
| `MorphOne` | `Expenditure` | `model_type` / `model_id` |
| `MorphTo` | `Revenue` | via `Revenue.linkable` |

### Campos Críticos

| Campo | Tipo | Descrição |
|---|---|---|
| `type` | string | `Transferência` ou outro (pró-labore, etc.) |
| `person_name` | string | Nome do beneficiário |
| `amount` | decimal | Valor bruto |
| `tax_percentage` | decimal | % INSS (padrão 11%) |
| `tax_amount` | decimal | Valor INSS calculado |
| `origin_bank_account_id` | FK | Conta de origem |
| `destination_bank_account_id` | FK | Conta de destino (Transferência) |
| `reference_date` | date | Competência |
| `paid_at` | date | Data de pagamento |
| `adjustment_amount` | decimal | Ajuste |

### O que acontece ao salvar (OutflowForm)

1. Calcula `tax_amount` (INSS)
2. Se `type = 'Transferência'`: valida que `destination_bank_account_id` está preenchido e é diferente da origem
3. `Outflow::updateOrCreate()`
4. Dispatch `outflow-saved`

> **Nota:** `BankAccount` não possui `HasMany outflows`. O Dashboard consulta outflows diretamente via `origin_bank_account_id`.

---

## Receipt (Recibos)

### Relacionamentos

| Tipo | Modelo | FK |
|---|---|---|
| `BelongsTo` | `Service` | `service_id` |
| `HasMedia` (Spatie) | — | `receipts` (PDF único), `signed_receipts` |

### Campos Críticos

| Campo | Tipo | Descrição |
|---|---|---|
| `receipt_number` | string | Formato `001/26` (seq/ano) — auto-gerado |
| `year` / `sequence` | int | Base da numeração anual |
| `status` | enum | `generated` → `signed` → `sent` |
| `is_signed` | bool | PDF assinado disponível |
| `is_sent` | bool | Enviado ao cliente |

### Hook de Modelo: `creating`

Auto-gera `receipt_number` no momento da criação:
```php
$sequence = Receipt::whereYear('created_at', $year)->max('sequence') + 1;
$receipt->receipt_number = sprintf('%03d/%s', $sequence, $yearShort); // ex: 001/26
```

### ReceiptService

**`generate(Service $service)`**
1. Busca Receipt existente do Service ou cria novo (triggering hook de auto-numeração)
2. Gera PDF via DomPDF com view `receipts.template`
3. Salva em temp → move para Spatie Media (`receipts`)
4. Atualiza `Service.is_documented = true`

**`delete(Receipt $receipt)`**
1. Limpa coleção de mídia `receipts`
2. Deleta o registro

---

## Fluxo Completo por Caso de Uso

### Prestação de Serviço Completa

```
1. Service criado
       │
       ├──[emitir NF]──→ Invoice salvo
       │                     └── Service.is_invoiced = true
       │                     └── Revenue.invoice_id = invoice.id
       │
       ├──[registrar recebimento]──→ Revenue salvo com paid_at
       │                                └── Service.is_paid = true
       │
       ├──[gerar recibo]──→ ReceiptService.generate()
       │                        └── Receipt criado (auto-numerado)
       │                        └── PDF gerado → Spatie Media
       │                        └── Service.is_documented = true
       │
       └──[imposto sobre receita]──→ Expenditure criada
                                         └── model = Revenue
                                         └── classification = 'Imposto'
                                         └── amount = revenue.tax_amount
```

### Pró-labore / Retirada

```
1. Outflow criado (tipo pró-labore)
       └── tax_amount calculado (INSS 11%)
       │
       └──[manualmente]──→ Expenditure criada
                               └── model = Outflow
                               └── classification = 'Imposto S/ Prolabore'
                               └── destination = 'INSS / Receita Federal'
                               └── amount = outflow.tax_amount
                               └── due_date = paid_at + 1 mês, dia 15
```

### Transferência entre Contas

```
1. Outflow criado (tipo Transferência)
       └── origin_bank_account_id = conta origem
       └── destination_bank_account_id = conta destino
       └── tax_amount = 0 (sem INSS)
```

---

## Flags de Status do Service

| Flag | Setada por | Quando |
|---|---|---|
| `is_paid` | `RevenueForm.save()` | Revenue.paid_at preenchido/limpo |
| `is_invoiced` | `InvoiceForm.save()` | Invoice vinculado/desvinculado ao Service |
| `is_documented` | `ReceiptService.generate()` | PDF gerado com sucesso |
