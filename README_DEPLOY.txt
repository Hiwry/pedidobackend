=== README - DEPLOY E CONFIGURAÇÃO ===

SISTEMA DE PEDIDOS - BACKEND LARAVEL
=====================================

=== INFORMAÇÕES DO PROJETO ===

Nome: Sistema de Pedidos Backend
Framework: Laravel 10.x
PHP: 8.2+
Banco: MySQL/SQLite
Versão Atual: 1.0.0
Última Atualização: 2025-01-17

=== ESTRUTURA DO PROJETO ===

app/
├── Http/Controllers/
│   ├── Admin/PersonalizationPriceController.php
│   ├── Api/ClientController.php
│   ├── KanbanController.php
│   └── OrderWizardController.php
├── Models/
│   ├── Order.php
│   ├── OrderItem.php
│   ├── OrderSublimation.php
│   └── PersonalizationPrice.php
└── Helpers/
    └── DateHelper.php

database/
├── migrations/
│   └── 2025_10_17_113552_add_seller_notes_and_colors_to_order_sublimations_table.php
└── seeders/

resources/views/
├── admin/personalization-prices/
├── kanban/pdf/
├── orders/wizard/
└── orders/edit-wizard/

=== CONFIGURAÇÃO INICIAL ===

1. INSTALAÇÃO
   - git clone [repositório]
   - cd backend
   - composer install
   - cp .env.example .env
   - php artisan key:generate

2. BANCO DE DADOS
   - Configurar .env com dados do banco
   - php artisan migrate
   - php artisan db:seed

3. PERMISSÕES
   - chmod -R 755 storage/
   - chmod -R 755 bootstrap/cache/

=== FUNCIONALIDADES PRINCIPAIS ===

🎯 GESTÃO DE PEDIDOS
- Criação de pedidos via wizard
- Edição de pedidos existentes
- Status de pedidos (Kanban)
- PDFs de personalização e costura

🎨 PERSONALIZAÇÃO
- Configuração de preços por tipo
- Cores separadas para SERIGRAFIA
- Observações do vendedor
- Detalhes específicos de cores

📊 RELATÓRIOS
- PDFs de personalização
- PDFs de costura
- Destaque para pedidos de evento

=== TIPOS DE PERSONALIZAÇÃO ===

1. SERIGRAFIA
   - Preços por tamanho (ESCUDO, A4, A3)
   - Cores adicionais com preços separados
   - Suporte a neon

2. EMBORRACHADO
   - Preços por tamanho
   - Contagem de cores
   - Suporte a neon

3. DTF
   - Preços por tamanho
   - Tamanhos dinâmicos

4. BORDADO
   - Preços por tamanho
   - Tamanhos dinâmicos

5. SUBLIMAÇÃO
   - Preços por tamanho
   - Tamanhos dinâmicos

=== COMANDOS ÚTEIS ===

# Desenvolvimento
php artisan serve
php artisan tinker
php artisan migrate:fresh --seed

# Produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Debug
php artisan log:clear
tail -f storage/logs/laravel.log

# Backup
php artisan backup:run

=== TROUBLESHOOTING ===

PROBLEMA: Erro de permissão
SOLUÇÃO: chmod -R 755 storage/ bootstrap/cache/

PROBLEMA: Migration não executa
SOLUÇÃO: php artisan migrate:status
         php artisan migrate --force

PROBLEMA: Cache não limpa
SOLUÇÃO: php artisan config:clear
         php artisan cache:clear
         php artisan view:clear

PROBLEMA: PDF não gera
SOLUÇÃO: Verificar extensão GD
         Verificar permissões de storage/

=== LOGS IMPORTANTES ===

- storage/logs/laravel.log (Logs gerais)
- storage/logs/error.log (Erros específicos)
- public/storage/ (Arquivos públicos)

=== CONTATOS ===

Desenvolvedor: Sistema de Pedidos
Repositório: GitHub
Documentação: Este arquivo
Suporte: Via issues no GitHub

=== VERSÕES ===

v1.0.0 - 2025-01-17
- Sistema completo de personalização
- Cores separadas para SERIGRAFIA
- Observações do vendedor
- Destaque para eventos
- Interface melhorada

=== FIM DO README ===
