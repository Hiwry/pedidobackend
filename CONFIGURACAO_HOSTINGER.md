# 🚀 Configuração para Hostinger - Sistema de Pedidos

## 📋 **Arquivos para Upload**

### **1. Estrutura de Pastas no Hostinger:**
```
public_html/
├── backend/          # Toda a pasta backend
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/       # Conteúdo da pasta public
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env          # Arquivo de configuração
│   └── artisan
└── .htaccess         # Arquivo de redirecionamento
```

### **2. Configuração do .env para Hostinger:**

Substitua o arquivo `.env` pelo conteúdo do arquivo `.env.hostinger` e ajuste as seguintes configurações:

```env
# Configurações Básicas
APP_NAME="Sistema de Pedidos"
APP_ENV=production
APP_KEY=base64:spxoU4+uz/6kDX/hfo1mOa3FwBX12gLUXPfnP8lJ72A=
APP_DEBUG=false
APP_URL=https://vestalize.com

# Configurações de Idioma
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

# Configurações de Log
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# Configurações do Banco de Dados (CREDENCIAIS CONFIGURADAS)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vestal30_novo_sistema
DB_USERNAME=admin_master
DB_PASSWORD=l(;Hk%+kiTS]

# Configurações de Sessão
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=vestalize.com

# Configurações de Email (AJUSTAR COM SUAS CREDENCIAIS)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=contato@vestalize.com      # ⚠️ SUBSTITUIR pelo email real
MAIL_PASSWORD=SUA_SENHA_DO_EMAIL         # ⚠️ SUBSTITUIR pela senha real
MAIL_FROM_ADDRESS="contato@vestalize.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### **3. Arquivo .htaccess para public_html:**

Crie um arquivo `.htaccess` na raiz do `public_html` com o seguinte conteúdo:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirecionar tudo para a pasta backend/public
    RewriteCond %{REQUEST_URI} !^/backend/public/
    RewriteRule ^(.*)$ /backend/public/$1 [L,QSA]
</IfModule>
```

### **4. Configuração do Banco de Dados:**

✅ **Credenciais já configuradas:**
- **Banco de dados**: `vestal30_novo_sistema`
- **Usuário**: `admin_master`
- **Senha**: `l(;Hk%+kiTS]`

**Passos:**
1. **Acesse o painel do Hostinger**
2. **Vá em "Bancos de Dados MySQL"**
3. **O banco `vestal30_novo_sistema` já deve existir**
4. **Importe o arquivo SQL** (`IMPORTAR_BANCO_HOSTINGER.sql`)

### **5. Permissões de Pastas:**

Configure as seguintes permissões no Hostinger:
- `storage/` → 755
- `bootstrap/cache/` → 755
- `public/storage` → 755

### **6. Comandos para Executar no Hostinger:**

Após o upload, execute os seguintes comandos via SSH ou Terminal do Hostinger:

```bash
# Navegar para a pasta do projeto
cd public_html/backend

# Instalar dependências (se necessário)
composer install --no-dev --optimize-autoloader

# Gerar chave da aplicação
php artisan key:generate

# Executar migrações
php artisan migrate

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **7. Configurações Adicionais:**

#### **A. Configuração de Email:**
- Acesse o painel do Hostinger
- Vá em "Email Accounts"
- Crie o email `contato@vestalize.com`
- Use as credenciais para configurar o SMTP

#### **B. Configuração de Domínio:**
- Certifique-se de que o domínio `vestalize.com` está apontando para o Hostinger
- Configure o SSL/HTTPS no painel

#### **C. Configuração de PHP:**
- Versão PHP: 8.1 ou superior
- Extensões necessárias: MySQL, GD, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath

### **8. Teste da Aplicação:**

Após a configuração, acesse:
- **URL Principal**: `https://vestalize.com`
- **Página de Produção**: `https://vestalize.com/producao`
- **Kanban de Produção**: `https://vestalize.com/producao/kanban`

### **9. Funcionalidades Implementadas:**

✅ **Página de Gerenciamento de Pedidos** (`/producao`)
- Filtros por período (hoje, semana, mês, personalizado)
- Filtros por tipo de personalização
- Filtros por status
- Busca por texto
- Estatísticas visuais

✅ **Kanban de Produção** (`/producao/kanban`)
- Mesmos filtros da listagem
- Cards visuais com drag & drop
- Nome do vendedor nos cards
- Modal detalhado

✅ **Melhorias no Kanban Existente**
- Nome do vendedor adicionado
- Seção do vendedor no modal

### **10. Suporte:**

Se houver problemas:
1. Verifique os logs em `storage/logs/laravel.log`
2. Confirme as configurações do banco de dados
3. Verifique as permissões das pastas
4. Teste a conexão com o banco via painel do Hostinger

---

**📝 Nota:** Lembre-se de substituir todas as credenciais marcadas com ⚠️ pelas suas credenciais reais do Hostinger.
