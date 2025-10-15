# ✅ Correções Aplicadas - Compatibilidade MySQL

## 📋 Problema Identificado

O sistema estava usando a função `strftime()` que é específica do **SQLite**, causando o seguinte erro ao usar **MySQL**:

```
SQLSTATE[42000]: Syntax error or access violation: 1305 
FUNCTION pedidos.strftime does not exist
```

---

## 🔧 Correções Aplicadas

### 1. DashboardController.php

**Arquivo:** `backend/app/Http/Controllers/DashboardController.php`

#### Correção 1: Faturamento Mensal (linha 37)

**Antes (SQLite):**
```php
DB::raw('strftime("%Y-%m", created_at) as mes')
```

**Depois (MySQL):**
```php
DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes')
```

#### Correção 2: Pedidos por Mês (linha 72)

**Antes (SQLite):**
```php
DB::raw('strftime("%Y-%m", created_at) as mes')
```

**Depois (MySQL):**
```php
DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes')
```

#### Correção 3: Top 5 Clientes (linha 53-58) 🆕

**Problema:** Erro `ONLY_FULL_GROUP_BY` - MySQL exige que todas as colunas não agregadas no SELECT estejam no GROUP BY.

**Antes:**
```php
$topClientes = Client::select('clients.*', DB::raw('COUNT(orders.id) as total_pedidos'), DB::raw('SUM(orders.total) as total_gasto'))
    ->join('orders', 'clients.id', '=', 'orders.client_id')
    ->groupBy('clients.id')  // ❌ Só agrupa por ID
    ->orderBy('total_gasto', 'desc')
    ->limit(5)
    ->get();
```

**Depois:**
```php
$topClientes = Client::select(
        'clients.id',
        'clients.name',
        'clients.phone_primary',
        'clients.phone_secondary',
        'clients.email',
        'clients.cpf_cnpj',
        'clients.address',
        'clients.city',
        'clients.state',
        'clients.zip_code',
        'clients.category',
        'clients.created_at',
        'clients.updated_at',
        DB::raw('COUNT(orders.id) as total_pedidos'),
        DB::raw('SUM(orders.total) as total_gasto')
    )
    ->join('orders', 'clients.id', '=', 'orders.client_id')
    ->groupBy(
        'clients.id',
        'clients.name',
        'clients.phone_primary',
        'clients.phone_secondary',
        'clients.email',
        'clients.cpf_cnpj',
        'clients.address',
        'clients.city',
        'clients.state',
        'clients.zip_code',
        'clients.category',
        'clients.created_at',
        'clients.updated_at'
    )  // ✅ Todas as colunas no GROUP BY
    ->orderBy('total_gasto', 'desc')
    ->limit(5)
    ->get();
```

---

## 🧪 Testes Realizados

### Testes de Compatibilidade

✅ **Teste 1**: Conexão com MySQL  
✅ **Teste 2**: Banco de dados configurado  
✅ **Teste 3**: Função DATE_FORMAT funcionando  
✅ **Teste 4**: Query de faturamento mensal  
✅ **Teste 5**: Query de pedidos por mês  
✅ **Teste 6**: Estatísticas gerais do Dashboard  

### Dados de Teste Criados

- **5 Status** (Aguardando Aprovação, Em Produção, Pronto, Entregue, Cancelado)
- **5 Clientes** de teste
- **15 Pedidos** distribuídos nos últimos 6 meses
- **25 Transações** de caixa (entradas e saídas)

**Resultados:**
- Faturamento Total: R$ 41.514,00
- Saldo Confirmado: R$ 15.481,17
- Saldo Pendente: R$ 8.970,91

---

## 📊 Diferenças entre SQLite e MySQL

### Formatação de Datas

| Função | SQLite | MySQL |
|--------|--------|-------|
| Formatar data | `strftime("%Y-%m", campo)` | `DATE_FORMAT(campo, "%Y-%m")` |
| Ano | `%Y` | `%Y` |
| Mês | `%m` | `%m` |
| Dia | `%d` | `%d` |

### Outras Diferenças Comuns

| Recurso | SQLite | MySQL |
|---------|--------|-------|
| Concatenação | `||` | `CONCAT()` |
| LIMIT com OFFSET | `LIMIT n OFFSET m` | `LIMIT m, n` |
| Auto Increment | `AUTOINCREMENT` | `AUTO_INCREMENT` |
| Booleano | 0/1 | `BOOLEAN` ou `TINYINT(1)` |

---

## ✅ Status Final

🎉 **Dashboard totalmente compatível com MySQL!**

### Funcionalidades Testadas:
- ✅ Estatísticas gerais (Total de pedidos, clientes, faturamento)
- ✅ Pedidos por status
- ✅ Faturamento mensal (últimos 6 meses)
- ✅ Pedidos por mês (últimos 12 meses)
- ✅ Pedidos recentes
- ✅ Top 5 clientes
- ✅ Pagamentos pendentes

### Correções Aplicadas:
1. ✅ Configuração do MySQL
2. ✅ Correção do saldo pendente
3. ✅ Função `strftime()` → `DATE_FORMAT()`
4. ✅ **Correção do GROUP BY (Top 5 Clientes)** 🆕

---

## 🚀 Como Testar

1. **Certifique-se de que o MySQL está rodando no XAMPP**

2. **Acesse o Dashboard:**
   ```bash
   php artisan serve
   ```
   Abra: http://localhost:8000

3. **Faça login:**
   - Email: `admin@pedidos.com`
   - Senha: `admin123`

4. **Verifique os gráficos e estatísticas**

---

## 📝 Arquivos Modificados

### backend/app/Http/Controllers/DashboardController.php
- Linha 37: `strftime` → `DATE_FORMAT` (faturamento mensal)
- Linha 53-88: Adicionadas todas as colunas no SELECT e GROUP BY (top 5 clientes) 🆕
- Linha 72: `strftime` → `DATE_FORMAT` (pedidos por mês)

### backend/app/Models/CashTransaction.php (correção anterior)
- Linha 72-77: `getSaldoGeral()` agora considera apenas transações confirmadas

### backend/.env (configuração anterior)
- `DB_CONNECTION=mysql` (antes era sqlite)
- `DB_DATABASE=pedidos`
- Credenciais do MySQL configuradas

---

## 🐛 Problemas Resolvidos

1. ✅ **"FUNCTION pedidos.strftime does not exist"**
   - Causa: Função SQLite usada com MySQL
   - Solução: Substituída por `DATE_FORMAT()`

2. ✅ **"isn't in GROUP BY" (ONLY_FULL_GROUP_BY)** 🆕
   - Causa: MySQL exige todas as colunas não agregadas no GROUP BY
   - Solução: Adicionadas todas as colunas de `clients` no GROUP BY

3. ✅ **Valores pendentes no saldo geral**
   - Causa: Query incluía status "pendente"
   - Solução: Filtro `where('status', 'confirmado')` adicionado

4. ✅ **SQLite sendo usado em produção**
   - Causa: Configuração padrão do Laravel
   - Solução: `.env` configurado para MySQL

---

## 📚 Referências

- [MySQL DATE_FORMAT()](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html#function_date-format)
- [Laravel Database: Query Builder](https://laravel.com/docs/12.x/queries)
- [Diferenças SQLite vs MySQL](https://www.sqlite.org/different.html)

---

**Data da correção:** 09/10/2025  
**Laravel:** 12.33.0  
**PHP:** 8.2.12  
**MySQL:** Via XAMPP

