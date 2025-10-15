# Instruções de Desenvolvimento Local

## ✅ Projeto Configurado com Sucesso!

O projeto foi preparado para ambiente de desenvolvimento local e está pronto para uso.

## 📋 Configurações Realizadas

1. ✅ Arquivos de teste removidos
2. ✅ Arquivo `.env` configurado para desenvolvimento local
3. ✅ Dependências do Composer instaladas
4. ✅ Chave da aplicação gerada
5. ✅ Banco de dados `backend` criado
6. ✅ Migrations executadas (41 tabelas criadas)
7. ✅ Seeders executados (dados iniciais carregados)
8. ✅ Cache limpo e otimizado
9. ✅ Link simbólico do storage criado

## 🗄️ Banco de Dados

- **Banco:** `backend`
- **Host:** 127.0.0.1
- **Porta:** 3306
- **Usuário:** root
- **Senha:** (vazio)

## 👤 Usuário Administrador Padrão

- **Email:** admin@example.com
- **Senha:** password

## 🚀 Como Iniciar o Servidor

### Opção 1: Servidor Artisan (Simples)
```bash
php artisan serve
```
Acesse: http://localhost:8000

### Opção 2: Servidor Completo (com Queue e Vite)
```bash
composer dev
```
Isso iniciará:
- Servidor PHP (porta 8000)
- Queue worker
- Logs em tempo real
- Vite dev server

### Opção 3: XAMPP
Como o projeto está em `C:\xampp\htdocs\backend\backend`, você pode acessar via:
- http://localhost/backend/backend/public

## 📦 Comandos Úteis

### Limpar Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Recriar Banco de Dados
```bash
php artisan migrate:fresh --seed
```

### Verificar Status do Projeto
```bash
php artisan about
```

### Acessar Console Interativo
```bash
php artisan tinker
```

## 🛠️ Estrutura do Projeto

- **app/Models** - Modelos do Eloquent
- **app/Http/Controllers** - Controladores
- **database/migrations** - Migrations do banco
- **database/seeders** - Seeders para popular dados
- **resources/views** - Views Blade
- **routes/web.php** - Rotas web
- **public** - Arquivos públicos

## 📝 Notas Importantes

1. O projeto usa **MariaDB 10.4.32** (via XAMPP)
2. O banco anterior `pedidos` estava corrompido e foi substituído por `backend`
3. Todas as tabelas foram criadas com sucesso
4. Os dados iniciais incluem:
   - Status do Kanban (Fila Corte, Cortado, Costura, etc.)
   - Configurações de preços
   - Opções de produtos
   - Tamanhos e preços de sublimação
   - Cores de serigrafia
   - Sobretaxas de tamanhos

## 🔧 Troubleshooting

### Erro de Conexão com Banco
Certifique-se que o MySQL do XAMPP está rodando:
- Abra o XAMPP Control Panel
- Inicie o módulo MySQL

### Erro de Permissão
Execute no PowerShell como Administrador:
```bash
php artisan storage:link
```

### Recriar Banco do Zero
```bash
php artisan migrate:fresh --seed
```

## 📚 Próximos Passos

1. Inicie o servidor: `php artisan serve`
2. Acesse: http://localhost:8000
3. Faça login com: admin@example.com / password
4. Comece a desenvolver!

---

**Ambiente:** Desenvolvimento Local  
**Laravel:** 12.33.0  
**PHP:** 8.2.12  
**Banco:** MySQL/MariaDB 10.4.32

