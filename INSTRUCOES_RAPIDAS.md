# ⚡ Instalação Rápida - Outro Computador

## 🚀 Método Automático (Recomendado)

### **Windows:**
1. Clone o repositório
2. Execute: `install.bat`
3. Siga as instruções na tela

### **Linux/macOS:**
1. Clone o repositório
2. Execute: `chmod +x install.sh && ./install.sh`
3. Siga as instruções na tela

## 🔧 Método Manual

### **1. Pré-requisitos:**
- PHP 8.1+
- Composer
- MySQL 5.7+

### **2. Comandos:**
```bash
# Clone o repositório
git clone https://github.com/SEU-USUARIO/sistema-pedidos-confeccoes.git
cd sistema-pedidos-confeccoes

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco no .env
# DB_DATABASE=nome_do_banco
# DB_USERNAME=usuario
# DB_PASSWORD=senha

# Executar migrações e seeds
php artisan migrate
php artisan db:seed

# Configurar storage
php artisan storage:link

# Iniciar servidor
php artisan serve
```

### **3. Acessar:**
- URL: `http://127.0.0.1:8000`
- Admin: `admin@admin.com` / `admin`
- Vendedor: `vendedor@vendedor.com` / `vendedor`

## 📱 Acesso Mobile/Remoto

### **Permitir acesso externo:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### **Acessar de outro dispositivo:**
- URL: `http://IP-DO-COMPUTADOR:8000`
- Exemplo: `http://192.168.1.100:8000`

## 🐛 Problemas Comuns

### **"Class 'PDO' not found"**
- Instale: `php8.1-pdo php8.1-mysql`

### **"GD extension not loaded"**
- Instale: `php8.1-gd`

### **"Storage link failed"**
```bash
rm public/storage
php artisan storage:link
```

### **"Permission denied"**
```bash
chmod -R 755 storage bootstrap/cache
```

## 📞 Suporte

- Logs: `storage/logs/laravel.log`
- Documentação completa: `INSTALACAO_OUTRO_COMPUTADOR.md`
