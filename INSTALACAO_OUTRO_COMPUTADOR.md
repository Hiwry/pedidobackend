# 🚀 Guia de Instalação - Outro Computador

Este guia mostra como instalar e executar o Sistema de Pedidos para Confecções em um novo computador.

## 📋 Pré-requisitos

### 1. **PHP 8.1+**
- **Windows**: Baixe do [php.net](https://windows.php.net/download/)
- **Linux**: `sudo apt install php8.1 php8.1-cli php8.1-mysql php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip`
- **macOS**: `brew install php@8.1`

### 2. **Composer**
- Baixe do [getcomposer.org](https://getcomposer.org/download/)
- **Windows**: Execute o instalador
- **Linux/macOS**: `curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer`

### 3. **MySQL 5.7+**
- **Windows**: [MySQL Installer](https://dev.mysql.com/downloads/installer/)
- **Linux**: `sudo apt install mysql-server`
- **macOS**: `brew install mysql`

### 4. **Node.js (Opcional)**
- Baixe do [nodejs.org](https://nodejs.org/)

## 🔧 Instalação Passo a Passo

### **Passo 1: Clonar o Repositório**

```bash
# Clone o repositório
git clone https://github.com/SEU-USUARIO/sistema-pedidos-confeccoes.git

# Entre na pasta do projeto
cd sistema-pedidos-confeccoes
```

### **Passo 2: Instalar Dependências PHP**

```bash
# Instalar dependências do Composer
composer install
```

### **Passo 3: Configurar Ambiente**

```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### **Passo 4: Configurar Banco de Dados**

1. **Crie um banco de dados MySQL:**
```sql
CREATE DATABASE sistema_pedidos_confeccoes;
```

2. **Configure o arquivo `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_pedidos_confeccoes
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### **Passo 5: Executar Migrações e Seeds**

```bash
# Executar migrações (criar tabelas)
php artisan migrate

# Executar seeds (dados iniciais)
php artisan db:seed
```

### **Passo 6: Configurar Storage**

```bash
# Criar link simbólico para storage
php artisan storage:link
```

### **Passo 7: Instalar Dependências Frontend (Opcional)**

```bash
# Instalar dependências do Node.js
npm install

# Compilar assets (se necessário)
npm run build
```

### **Passo 8: Iniciar o Servidor**

```bash
# Iniciar servidor de desenvolvimento
php artisan serve
```

O sistema estará disponível em: `http://127.0.0.1:8000`

## 👥 Usuários Padrão

Após executar as seeds, você terá:

- **Administrador**: 
  - Email: `admin@admin.com`
  - Senha: `admin`

- **Vendedor**: 
  - Email: `vendedor@vendedor.com`
  - Senha: `vendedor`

## 🔧 Configurações Adicionais

### **Configurar Permissões (Linux/macOS)**

```bash
# Dar permissões corretas
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### **Configurar Servidor Web (Produção)**

#### **Apache (.htaccess)**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### **Nginx**
```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /caminho/para/projeto/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🐛 Solução de Problemas

### **Erro: "Class 'PDO' not found"**
```bash
# Instalar extensão PDO
sudo apt install php8.1-pdo php8.1-mysql
```

### **Erro: "GD extension not loaded"**
```bash
# Instalar extensão GD
sudo apt install php8.1-gd
```

### **Erro: "mbstring extension not loaded"**
```bash
# Instalar extensão mbstring
sudo apt install php8.1-mbstring
```

### **Erro: "Storage link failed"**
```bash
# Remover link existente e recriar
rm public/storage
php artisan storage:link
```

### **Erro: "Permission denied"**
```bash
# Dar permissões corretas
sudo chmod -R 755 storage
sudo chmod -R 755 bootstrap/cache
```

## 📦 Comandos Úteis

### **Limpar Cache**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### **Verificar Status**
```bash
php artisan --version
composer --version
php -v
mysql --version
```

### **Backup do Banco**
```bash
mysqldump -u usuario -p sistema_pedidos_confeccoes > backup.sql
```

### **Restaurar Backup**
```bash
mysql -u usuario -p sistema_pedidos_confeccoes < backup.sql
```

## 🔄 Atualizações

### **Atualizar Código**
```bash
# Baixar atualizações
git pull origin main

# Instalar novas dependências
composer install

# Executar novas migrações
php artisan migrate

# Limpar cache
php artisan cache:clear
```

## 📱 Acesso Mobile

O sistema é responsivo e funciona em dispositivos móveis:
- Acesse pelo navegador do celular
- URL: `http://IP-DO-SERVIDOR:8000`
- Exemplo: `http://192.168.1.100:8000`

## 🌐 Acesso Remoto

### **Permitir Acesso Externo**
```bash
# Iniciar servidor com IP específico
php artisan serve --host=0.0.0.0 --port=8000
```

### **Configurar Firewall**
```bash
# Linux (UFW)
sudo ufw allow 8000

# Windows Firewall
# Adicionar regra para porta 8000
```

## 📞 Suporte

Se encontrar problemas:

1. **Verifique os logs**: `storage/logs/laravel.log`
2. **Verifique as permissões** dos arquivos
3. **Verifique a configuração** do banco de dados
4. **Verifique as extensões PHP** necessárias

## ✅ Checklist de Instalação

- [ ] PHP 8.1+ instalado
- [ ] Composer instalado
- [ ] MySQL instalado e configurado
- [ ] Repositório clonado
- [ ] Dependências instaladas (`composer install`)
- [ ] Arquivo `.env` configurado
- [ ] Chave da aplicação gerada
- [ ] Banco de dados criado
- [ ] Migrações executadas
- [ ] Seeds executadas
- [ ] Storage link criado
- [ ] Servidor iniciado
- [ ] Acesso testado no navegador

---

**🎉 Parabéns! Seu sistema está funcionando!**
