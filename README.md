# Ninja Manager

Sistema de gestão empresarial desenvolvido com Laravel 12, Livewire 4 e TallStackUI.

## 🚀 Tecnologias

- **Laravel 12** - Framework PHP
- **Livewire 3** - Framework full-stack reativo
- **TallStackUI** - Componentes UI prontos
- **Tailwind CSS** - Framework CSS utility-first
- **SQLite** - Banco de dados

## 📋 Funcionalidades

### Gestão de Executantes

- Cadastro de colaboradores executantes
- Vinculação com múltiplas funções
- Busca por nome ou CPF
- Validação de documento único

### Gestão de Funções

- Cadastro de funções/cargos
- Contagem de executantes por função
- CRUD completo

### Gestão de Itens de Serviço

- Cadastro de serviços com código único
- Descrição pública e interna
- Valor unitário com 4 casas decimais
- Busca por código ou descrição

### Gestão de Clientes

- Cadastro de Pessoa Física (PF) e Jurídica (PJ)
- Endereço completo com todos os campos
- Validação de CPF/CNPJ único
- Busca por nome ou documento

## 🛠️ Instalação

```bash
# Clonar o repositório
cd /home/csouza/projects/ninja3d/docs/ninja-manager

# Instalar dependências PHP
composer install

# Instalar dependências Node
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Executar migrations
php artisan migrate

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## 📁 Estrutura do Banco de Dados

### Tabelas

- **executors** - Executantes/Colaboradores
- **roles** - Funções/Cargos
- **executor_role** - Relacionamento many-to-many
- **service_items** - Itens de serviço
- **clients** - Clientes (PF/PJ)

### Relacionamentos

- Um executante pode ter múltiplas funções
- Uma função pode ter múltiplos executantes
- Documentos (CPF/CNPJ) são únicos em todo o sistema

## 🎨 Interface

- Dashboard com estatísticas
- Navegação intuitiva
- Modais para criar/editar
- Tabelas responsivas
- Busca em tempo real
- Feedback visual com toasts

## 🔧 Desenvolvimento

```bash
# Modo desenvolvimento (watch)
npm run dev

# Em outro terminal
php artisan serve
```

## 📝 Rotas

- `/` - Dashboard
- `/executors` - Gestão de Executantes
- `/roles` - Gestão de Funções
- `/service-items` - Gestão de Itens de Serviço
- `/clients` - Gestão de Clientes

## 🎯 Próximos Passos

- [ ] Integração com template de recibos
- [ ] API REST para consumo externo
- [ ] Autenticação de usuários
- [ ] Relatórios e exportações
- [ ] Backup automático
