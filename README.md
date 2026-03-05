# Ninja Manager

Sistema de gestão empresarial completo, desenvolvido com **Laravel 12**, **Livewire 3** e **Tailwind CSS 4**. Cobre desde o controle operacional de serviços e equipe até um módulo financeiro completo com dashboard analítico.

---

## 🚀 Stack Tecnológica

| Camada           | Tecnologia                     |
| ---------------- | ------------------------------ |
| Backend          | PHP 8.4 + Laravel 12           |
| Frontend reativo | Livewire 3 + Alpine.js         |
| Estilo           | Tailwind CSS 4 + DaisyUI 5     |
| Banco de dados   | SQLite (portável, zero config) |
| Testes           | PHPUnit 11                     |
| Media / Uploads  | Spatie Media Library           |

---

## 📋 Módulos

### 👥 Gestão de Equipe

- **Funções** — Cadastro de cargos/funções com contagem de executantes
- **Executantes** — Colaboradores com múltiplas funções, busca por CPF/nome
- **Horas Trabalhadas** — Controle de horas por cliente, modalidade e contrato

### 🧑‍💼 Gestão de Clientes

- Pessoa Física e Jurídica (CPF/CNPJ único)
- Endereço completo com todos os campos
- Busca filtrada por nome ou documento

### 🔧 Gestão de Serviços

- Vinculação cliente × executante × função
- Itens de serviço com código, descrição interna/pública e valor unitário
- Status e flags de pagamento, documentação e emissão de NF
- Geração de **Recibos** impressos em PDF (template A4 profissional)

### 💰 Módulo Financeiro

- **Dashboard Analítico** — Análise de fluxo de caixa por período (mensal/trimestral), gráficos, saldo parcial e final real e previsto
- **Receitas** — Receitas manuais e vinculadas a serviços/NFs, com controle de imposto e data de recebimento
- **Despesas** — Despesas classificadas, com conta bancária e controle de pagamento. Suporte a despesas geradas automaticamente (impostos de receitas)
- **Saídas (Outflows)** — Retiradas e pro-labore, com suporte a transferências entre contas e tributação
- **Notas Fiscais** — Registro de NF-e com chave de acesso, XML/PDF anexado, vinculação a serviço/cliente
- **Contas Bancárias** — Cadastro de contas para reconciliação de pagamentos
- **Impostos** — Visão consolidada de impostos pagos, provisionados e pendentes

### ⚙️ Configurações

- **Backup & Restore** — Exporta um `.zip` completo (banco de dados JSON v2.0 + arquivos de storage). Importação inteligente com ID remapping e relatório de restauração

---

## 🛠️ Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/your-org/ninja-manager.git
cd ninja-manager

# 2. Instalar dependências
composer install
npm install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Configurar dados da empresa (recibos, branding)
# Edite o .env com os valores da sua empresa:
# COMPANY_NAME, COMPANY_DOCUMENT, COMPANY_ADDRESS, COMPANY_EMAIL
# COMPANY_OWNER_NAME, COMPANY_OWNER_DOCUMENT, APP_TAGLINE

# 5. Banco de dados
php artisan migrate

# 6. Symlink de storage (para logos e uploads)
php artisan storage:link

# 7. Compilar assets
npm run build

# 8. Iniciar
php artisan serve
```

---

## 🗂️ Rotas Principais

| Rota                       | Descrição            |
| -------------------------- | -------------------- |
| `/`                        | Dashboard geral      |
| `/services`                | Gestão de serviços   |
| `/clients`                 | Gestão de clientes   |
| `/clients/work-hours`      | Horas trabalhadas    |
| `/executors`               | Executantes          |
| `/roles`                   | Funções/Cargos       |
| `/service-items`           | Itens de serviço     |
| `/receipts`                | Recibos              |
| `/financial/dashboard`     | Dashboard financeiro |
| `/financial/revenues`      | Receitas             |
| `/financial/expenditures`  | Despesas             |
| `/financial/outflows`      | Saídas / Retiradas   |
| `/financial/invoices`      | Notas Fiscais        |
| `/financial/taxes`         | Impostos             |
| `/financial/bank-accounts` | Contas Bancárias     |
| `/settings/database`       | Backup & Restore     |

---

## 🔒 Dados Sensíveis

Este projeto usa `config/company.php` para dados específicos de cada instalação (nome da empresa, CNPJ, endereço, etc.). Esses valores são lidos exclusivamente do `.env` e **nunca devem ser commitados**.

Copie `.env.example` e preencha as variáveis `COMPANY_*` com os dados da sua empresa.

---

## 🧪 Testes

```bash
# Todos os testes
php artisan test --compact

# Filtro por nome
php artisan test --compact --filter=NomeDoTeste
```

---

## 🗃️ Backup & Restore

O sistema possui backup integrado em `settings/database`:

- **Exportar** → gera um `.zip` com `database.json` (todos os dados) + arquivos de storage
- **Importar** → restaura o ambiente completo com remapeamento inteligente de IDs e suporte a morphs
