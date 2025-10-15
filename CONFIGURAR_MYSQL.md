# Configuração do MySQL no XAMPP

## Passo 1: Criar o banco de dados

1. Abra o XAMPP Control Panel
2. Inicie o MySQL
3. Clique em "Admin" do MySQL para abrir o phpMyAdmin
4. Crie um novo banco de dados chamado `pedidos` com collation `utf8mb4_unicode_ci`

Ou execute este SQL no phpMyAdmin:

```sql
CREATE DATABASE IF NOT EXISTS pedidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Passo 2: Configurar o arquivo .env

Como o arquivo `.env` não pode ser criado automaticamente, você precisa criar manualmente:

1. Navegue até a pasta `backend`
2. Crie um arquivo chamado `.env` (sem extensão)
3. Cole o conteúdo abaixo:

```env
APP_NAME="Sistema de Pedidos"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8000

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pedidos
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

## Passo 3: Executar os comandos de configuração

Abra o terminal (PowerShell) na pasta `backend` e execute:

```bash
# Gerar a chave da aplicação
php artisan key:generate

# Limpar cache de configuração
php artisan config:clear

# Executar as migrations (criar as tabelas)
php artisan migrate

# Popular o banco com dados iniciais (opcional)
php artisan db:seed
```

## Passo 4: Verificar a conexão

Execute este comando para verificar se a conexão está funcionando:

```bash
php artisan tinker
```

Depois execute:

```php
DB::connection()->getPdo();
exit;
```

Se não houver erro, a conexão está funcionando!

## Correção Aplicada

✅ O método `getSaldoGeral()` foi corrigido para considerar apenas transações **confirmadas**, não incluindo mais valores pendentes no saldo geral.

Agora:
- **Saldo Atual**: Soma apenas transações confirmadas
- **Saldo Geral**: Soma apenas transações confirmadas (era o problema)
- **Saldo Pendente**: Soma apenas transações com status pendente

## Problemas Comuns

### Erro "Access denied for user 'root'@'localhost'"

Se o MySQL do XAMPP tiver senha, edite o `.env`:
```env
DB_PASSWORD=sua_senha_aqui
```

### Erro "SQLSTATE[HY000] [2002] No connection could be made"

Verifique se o MySQL está rodando no XAMPP Control Panel.

### Erro "Base table or view not found"

Execute as migrations:
```bash
php artisan migrate
```

