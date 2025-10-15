#!/bin/bash

# 🚀 Script de Instalação Automática - Sistema de Pedidos para Confecções
# Para Linux/macOS

echo "🚀 Iniciando instalação do Sistema de Pedidos para Confecções..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar se o PHP está instalado
check_php() {
    print_status "Verificando PHP..."
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
        print_success "PHP $PHP_VERSION encontrado"
        return 0
    else
        print_error "PHP não encontrado. Instale PHP 8.1+ primeiro."
        return 1
    fi
}

# Verificar se o Composer está instalado
check_composer() {
    print_status "Verificando Composer..."
    if command -v composer &> /dev/null; then
        print_success "Composer encontrado"
        return 0
    else
        print_error "Composer não encontrado. Instale o Composer primeiro."
        return 1
    fi
}

# Verificar se o MySQL está instalado
check_mysql() {
    print_status "Verificando MySQL..."
    if command -v mysql &> /dev/null; then
        print_success "MySQL encontrado"
        return 0
    else
        print_warning "MySQL não encontrado. Certifique-se de ter o MySQL instalado."
        return 1
    fi
}

# Instalar dependências PHP
install_php_deps() {
    print_status "Instalando dependências PHP..."
    
    # Detectar sistema operacional
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        # Linux
        sudo apt update
        sudo apt install -y php8.1-cli php8.1-mysql php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip php8.1-curl
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        if command -v brew &> /dev/null; then
            brew install php@8.1
        else
            print_warning "Homebrew não encontrado. Instale as extensões PHP manualmente."
        fi
    fi
}

# Instalar dependências do projeto
install_dependencies() {
    print_status "Instalando dependências do projeto..."
    
    if [ ! -f "composer.json" ]; then
        print_error "Arquivo composer.json não encontrado. Execute este script na pasta do projeto."
        exit 1
    fi
    
    composer install --no-dev --optimize-autoloader
    print_success "Dependências instaladas"
}

# Configurar ambiente
setup_environment() {
    print_status "Configurando ambiente..."
    
    if [ ! -f ".env" ]; then
        cp .env.example .env
        print_success "Arquivo .env criado"
    else
        print_warning "Arquivo .env já existe"
    fi
    
    # Gerar chave da aplicação
    php artisan key:generate
    print_success "Chave da aplicação gerada"
}

# Configurar banco de dados
setup_database() {
    print_status "Configurando banco de dados..."
    
    # Solicitar informações do banco
    echo -e "${YELLOW}Configuração do Banco de Dados:${NC}"
    read -p "Nome do banco: " DB_NAME
    read -p "Usuário do banco: " DB_USER
    read -s -p "Senha do banco: " DB_PASS
    echo
    
    # Atualizar arquivo .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    
    print_success "Configuração do banco atualizada"
}

# Executar migrações
run_migrations() {
    print_status "Executando migrações..."
    
    php artisan migrate --force
    print_success "Migrações executadas"
}

# Executar seeds
run_seeds() {
    print_status "Executando seeds..."
    
    php artisan db:seed --force
    print_success "Seeds executadas"
}

# Configurar storage
setup_storage() {
    print_status "Configurando storage..."
    
    php artisan storage:link
    print_success "Storage configurado"
}

# Configurar permissões
setup_permissions() {
    print_status "Configurando permissões..."
    
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
    
    # Se estiver rodando como root, mudar ownership
    if [ "$EUID" -eq 0 ]; then
        chown -R www-data:www-data storage bootstrap/cache
    fi
    
    print_success "Permissões configuradas"
}

# Limpar cache
clear_cache() {
    print_status "Limpando cache..."
    
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    php artisan route:clear
    
    print_success "Cache limpo"
}

# Função principal
main() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}  Sistema de Pedidos - Instalação${NC}"
    echo -e "${BLUE}================================${NC}"
    echo
    
    # Verificações
    if ! check_php; then
        print_error "Instale o PHP 8.1+ primeiro"
        exit 1
    fi
    
    if ! check_composer; then
        print_error "Instale o Composer primeiro"
        exit 1
    fi
    
    check_mysql
    
    # Instalação
    install_dependencies
    setup_environment
    setup_database
    run_migrations
    run_seeds
    setup_storage
    setup_permissions
    clear_cache
    
    echo
    echo -e "${GREEN}================================${NC}"
    echo -e "${GREEN}  Instalação Concluída!${NC}"
    echo -e "${GREEN}================================${NC}"
    echo
    echo -e "${YELLOW}Usuários padrão:${NC}"
    echo -e "  Admin: admin@admin.com / admin"
    echo -e "  Vendedor: vendedor@vendedor.com / vendedor"
    echo
    echo -e "${YELLOW}Para iniciar o servidor:${NC}"
    echo -e "  php artisan serve"
    echo
    echo -e "${YELLOW}Acesse:${NC} http://127.0.0.1:8000"
    echo
}

# Executar função principal
main "$@"
