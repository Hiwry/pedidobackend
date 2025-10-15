@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

REM 🚀 Script de Instalação Automática - Sistema de Pedidos para Confecções
REM Para Windows

echo.
echo 🚀 Iniciando instalação do Sistema de Pedidos para Confecções...
echo.

REM Verificar se o PHP está instalado
echo [INFO] Verificando PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP não encontrado. Instale PHP 8.1+ primeiro.
    echo Baixe em: https://windows.php.net/download/
    pause
    exit /b 1
)
echo [SUCCESS] PHP encontrado

REM Verificar se o Composer está instalado
echo [INFO] Verificando Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer não encontrado. Instale o Composer primeiro.
    echo Baixe em: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo [SUCCESS] Composer encontrado

REM Verificar se o MySQL está instalado
echo [INFO] Verificando MySQL...
mysql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] MySQL não encontrado. Certifique-se de ter o MySQL instalado.
)

REM Verificar se estamos na pasta correta
if not exist "composer.json" (
    echo [ERROR] Arquivo composer.json não encontrado. Execute este script na pasta do projeto.
    pause
    exit /b 1
)

REM Instalar dependências do projeto
echo [INFO] Instalando dependências do projeto...
composer install --no-dev --optimize-autoloader
if %errorlevel% neq 0 (
    echo [ERROR] Erro ao instalar dependências
    pause
    exit /b 1
)
echo [SUCCESS] Dependências instaladas

REM Configurar ambiente
echo [INFO] Configurando ambiente...
if not exist ".env" (
    copy ".env.example" ".env" >nul
    echo [SUCCESS] Arquivo .env criado
) else (
    echo [WARNING] Arquivo .env já existe
)

REM Gerar chave da aplicação
echo [INFO] Gerando chave da aplicação...
php artisan key:generate
if %errorlevel% neq 0 (
    echo [ERROR] Erro ao gerar chave da aplicação
    pause
    exit /b 1
)
echo [SUCCESS] Chave da aplicação gerada

REM Configurar banco de dados
echo.
echo [INFO] Configuração do Banco de Dados:
set /p DB_NAME="Nome do banco: "
set /p DB_USER="Usuário do banco: "
set /p DB_PASS="Senha do banco: "

REM Atualizar arquivo .env
powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=%DB_NAME%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=%DB_USER%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=%DB_PASS%' | Set-Content .env"

echo [SUCCESS] Configuração do banco atualizada

REM Executar migrações
echo [INFO] Executando migrações...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Erro ao executar migrações. Verifique a configuração do banco.
    pause
    exit /b 1
)
echo [SUCCESS] Migrações executadas

REM Executar seeds
echo [INFO] Executando seeds...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo [ERROR] Erro ao executar seeds
    pause
    exit /b 1
)
echo [SUCCESS] Seeds executadas

REM Configurar storage
echo [INFO] Configurando storage...
php artisan storage:link
if %errorlevel% neq 0 (
    echo [WARNING] Erro ao configurar storage link
) else (
    echo [SUCCESS] Storage configurado
)

REM Limpar cache
echo [INFO] Limpando cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo [SUCCESS] Cache limpo

echo.
echo ================================
echo   Instalação Concluída!
echo ================================
echo.
echo Usuários padrão:
echo   Admin: admin@admin.com / admin
echo   Vendedor: vendedor@vendedor.com / vendedor
echo.
echo Para iniciar o servidor:
echo   php artisan serve
echo.
echo Acesse: http://127.0.0.1:8000
echo.
pause
