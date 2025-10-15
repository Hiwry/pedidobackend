# Script de Configuração MySQL para Sistema de Pedidos
# Execute este script no PowerShell como administrador

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "Configuração do MySQL - Sistema de Pedidos" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se está na pasta backend
$currentPath = Get-Location
if (-not $currentPath.Path.EndsWith("backend")) {
    Write-Host "ERRO: Execute este script dentro da pasta 'backend'" -ForegroundColor Red
    exit 1
}

# Verificar se o arquivo .env existe
if (-not (Test-Path ".env")) {
    Write-Host "⚠️  Arquivo .env não encontrado. Criando..." -ForegroundColor Yellow
    
    $envContent = @"
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
MAIL_FROM_NAME=`"`${APP_NAME}`"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="`${APP_NAME}"
"@
    
    Set-Content -Path ".env" -Value $envContent -Encoding UTF8
    Write-Host "✅ Arquivo .env criado com sucesso!" -ForegroundColor Green
} else {
    Write-Host "✅ Arquivo .env já existe" -ForegroundColor Green
}

Write-Host ""
Write-Host "Passo 1: Gerando chave da aplicação..." -ForegroundColor Yellow
php artisan key:generate
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Chave gerada com sucesso!" -ForegroundColor Green
} else {
    Write-Host "❌ Erro ao gerar chave" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Passo 2: Limpando cache de configuração..." -ForegroundColor Yellow
php artisan config:clear
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Cache limpo com sucesso!" -ForegroundColor Green
} else {
    Write-Host "❌ Erro ao limpar cache" -ForegroundColor Red
}

Write-Host ""
Write-Host "Passo 3: Testando conexão com o banco de dados..." -ForegroundColor Yellow
Write-Host "⚠️  Certifique-se de que o MySQL está rodando no XAMPP!" -ForegroundColor Cyan

# Tentar conectar ao banco
$testConnection = php artisan tinker --execute="echo 'Conectado!'; DB::connection()->getPdo(); exit;"
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Conexão com MySQL estabelecida!" -ForegroundColor Green
} else {
    Write-Host "❌ Erro ao conectar com MySQL" -ForegroundColor Red
    Write-Host ""
    Write-Host "Verifique:" -ForegroundColor Yellow
    Write-Host "  1. O MySQL está rodando no XAMPP" -ForegroundColor White
    Write-Host "  2. O banco 'pedidos' foi criado no phpMyAdmin" -ForegroundColor White
    Write-Host "  3. As credenciais no arquivo .env estão corretas" -ForegroundColor White
    Write-Host ""
    $continue = Read-Host "Deseja continuar mesmo assim? (s/n)"
    if ($continue -ne "s") {
        exit 1
    }
}

Write-Host ""
Write-Host "Passo 4: Executando migrations..." -ForegroundColor Yellow
php artisan migrate
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migrations executadas com sucesso!" -ForegroundColor Green
} else {
    Write-Host "❌ Erro ao executar migrations" -ForegroundColor Red
    Write-Host ""
    Write-Host "Você precisa criar o banco de dados 'pedidos' no phpMyAdmin primeiro!" -ForegroundColor Yellow
    Write-Host "Execute no phpMyAdmin: CREATE DATABASE pedidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Cyan
    exit 1
}

Write-Host ""
Write-Host "Passo 5: Populando banco com dados iniciais (opcional)..." -ForegroundColor Yellow
$seed = Read-Host "Deseja popular o banco com dados de exemplo? (s/n)"
if ($seed -eq "s") {
    php artisan db:seed
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Dados inseridos com sucesso!" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Alguns seeders falharam, mas pode continuar" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "=====================================" -ForegroundColor Green
Write-Host "✅ Configuração concluída!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "Correções aplicadas:" -ForegroundColor Cyan
Write-Host "  ✅ Banco de dados configurado para MySQL" -ForegroundColor White
Write-Host "  ✅ Saldo Geral agora considera apenas transações CONFIRMADAS" -ForegroundColor White
Write-Host "  ✅ Saldo Pendente mostra apenas valores pendentes" -ForegroundColor White
Write-Host ""
Write-Host "Para iniciar o servidor, execute:" -ForegroundColor Yellow
Write-Host "  php artisan serve" -ForegroundColor Cyan
Write-Host ""

