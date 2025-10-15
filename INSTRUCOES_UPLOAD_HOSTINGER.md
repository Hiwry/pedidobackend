# 🚀 INSTRUÇÕES PARA UPLOAD NO HOSTINGER

## 📋 **Configuração Finalizada**

✅ **Arquivos prontos para upload:**
- `.env` - Configurado para vestalize.com
- `IMPORTAR_BANCO_HOSTINGER.sql` - Script do banco
- `htaccess-public_html` - Para a raiz do public_html

## 🎯 **Passo a Passo para Upload**

### **1. Estrutura de Pastas no Hostinger:**

```
public_html/
├── backend/              # Toda a pasta backend
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/          # Pasta public do Laravel
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env             # Arquivo de configuração
│   ├── .htaccess        # Redirecionamento interno
│   └── artisan
├── .htaccess            # Arquivo da raiz (htaccess-public_html)
└── index.php            # (opcional) Redirecionamento
```

### **2. Upload dos Arquivos:**

1. **Faça upload de TODA a pasta `backend`** para o `public_html`
2. **Renomeie `backend` para `sistema`** (ou mantenha como `backend`)
3. **Copie o conteúdo de `htaccess-public_html`** para `.htaccess` na raiz do `public_html`

### **3. Configuração do Banco de Dados:**

1. **Acesse o painel do Hostinger**
2. **Vá em "Bancos de Dados MySQL"**
3. **Importe o arquivo `IMPORTAR_BANCO_HOSTINGER.sql`**

**Credenciais já configuradas:**
- Banco: `vestal30_novo_sistema`
- Usuário: `admin_master`
- Senha: `l(;Hk%+kiTS]`

### **4. Configuração de Permissões:**

Configure as seguintes permissões no Hostinger:
- `storage/` → 755
- `bootstrap/cache/` → 755
- `public/storage` → 755

### **5. Comandos para Executar (via SSH ou Terminal do Hostinger):**

```bash
# Navegar para a pasta do projeto
cd public_html/backend

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

### **6. Configuração de Email (Opcional):**

1. **Acesse o painel do Hostinger**
2. **Vá em "Email Accounts"**
3. **Crie o email `contato@vestalize.com`**
4. **Atualize a senha no arquivo `.env`**

### **7. Teste da Aplicação:**

Após o upload, acesse:
- **URL Principal**: `https://vestalize.com`
- **Página de Produção**: `https://vestalize.com/producao`
- **Kanban de Produção**: `https://vestalize.com/producao/kanban`

### **8. Funcionalidades Disponíveis:**

✅ **Página de Gerenciamento** (`/producao`)
- Filtros por período (hoje, semana, mês, personalizado)
- Filtros por tipo de personalização
- Filtros por status
- Busca por texto
- Estatísticas visuais
- Tabela com nome do vendedor

✅ **Kanban de Produção** (`/producao/kanban`)
- Cards visuais com drag & drop
- Nome do vendedor nos cards
- Filtros avançados
- Modal detalhado

✅ **Melhorias no Kanban Existente**
- Vendedor adicionado nos cards
- Seção do vendedor no modal

### **9. Resolução de Problemas:**

Se houver problemas:

1. **Verifique os logs** em `storage/logs/laravel.log`
2. **Confirme as permissões** das pastas
3. **Teste a conexão** com o banco via painel do Hostinger
4. **Verifique se o PHP** está na versão 8.1 ou superior

### **10. URLs de Acesso:**

- **Sistema Principal**: `https://vestalize.com`
- **Lista de Pedidos**: `https://vestalize.com/pedidos`
- **Novo Pedido**: `https://vestalize.com/pedidos/novo`
- **Kanban Geral**: `https://vestalize.com/kanban`
- **Produção**: `https://vestalize.com/producao`
- **Kanban Produção**: `https://vestalize.com/producao/kanban`

---

## 🎉 **Sistema Pronto para Produção!**

O sistema está completamente configurado e pronto para uso no Hostinger com todas as funcionalidades implementadas.

