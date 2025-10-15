# 🧪 Teste Manual - PDF com Imagens

## Como Testar a Correção

### Opção 1: Via Interface Web (Recomendado)

1. **Acesse o sistema**
   ```
   http://localhost:8000
   ```

2. **Faça login** (se necessário)

3. **Vá para o Kanban**
   - Menu → Kanban

4. **Escolha um pedido com imagem**
   - Pedidos testados: #20, #21, #23, #24
   - Qualquer pedido que tenha imagem de capa

5. **Baixe a Folha de Costura**
   - Clique no pedido
   - Clique em "Baixar Folha de Costura"
   - O PDF deve ser baixado

6. **Verifique o PDF**
   - Abra o PDF baixado
   - ✅ A imagem de capa deve aparecer
   - ✅ A imagem deve estar redimensionada
   - ✅ Qualidade deve estar boa

### Opção 2: Via Linha de Comando

```powershell
# Navegar para o diretório
Set-Location -Path "C:\xampp\htdocs\backend\backend"

# Criar script de teste
$testScript = @'
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$orderId = 20; // Altere para o ID do pedido que deseja testar
$controller = new \App\Http\Controllers\KanbanController();
$response = $controller->downloadCostura($orderId);

echo "✅ PDF gerado com sucesso!\n";
echo "O arquivo foi baixado.\n";
'@

# Salvar e executar
$testScript | Out-File -FilePath "test_download.php" -Encoding UTF8
php test_download.php
Remove-Item "test_download.php"
```

## ✅ O Que Deve Acontecer

### Sucesso ✓

- ✅ PDF é baixado sem erros
- ✅ Imagem de capa aparece no PDF
- ✅ Imagem está bem posicionada
- ✅ Qualidade da imagem está boa
- ✅ Tamanho do PDF é razoável (15-25 KB)

### Se Algo Der Errado ✗

#### Imagem não aparece

**Verifique:**
1. A extensão GD está habilitada?
   ```powershell
   php -r "echo extension_loaded('gd') ? 'GD OK' : 'GD NAO HABILITADA';"
   ```

2. O arquivo de imagem existe?
   ```powershell
   Get-ChildItem -Path "storage\app\public\orders\covers\" -Recurse
   ```

3. Há erros no log do Laravel?
   ```powershell
   Get-Content "storage\logs\laravel.log" -Tail 50
   ```

#### Erro ao gerar PDF

**Verifique:**
1. Cache do Laravel
   ```powershell
   php artisan view:clear
   php artisan cache:clear
   ```

2. Permissões das pastas
   ```powershell
   # As pastas storage e bootstrap/cache precisam ter permissão de escrita
   ```

## 📊 Pedidos com Imagens para Teste

| Pedido ID | Cliente | Imagem |
|-----------|---------|--------|
| 20 | Hiwry Keveny | ✅ PNG 1920x1080 |
| 21 | Hiwry Keveny | ✅ JPG |
| 23 | Hiwry Keveny | ✅ PNG |
| 24 | Hiwry Keveny | ✅ JPG |

## 🎯 Resultado Esperado

Ao abrir o PDF, você deve ver:

```
┌─────────────────────────────────────┐
│     FOLHA DE COSTURA                │
│     Pedido #000020                  │
├─────────────────────────────────────┤
│ CLIENTE                             │
│ Nome: Hiwry Keveny Rocha...         │
│ Tel: 82983395637                    │
│ Entrega: 04/11/2025                 │
├─────────────────────────────────────┤
│ ITEM 1 - 15 peças                   │
│                                     │
│ ┌───────────────────────────────┐   │
│ │         CAPA                  │   │
│ │  [IMAGEM DEVE APARECER AQUI]  │   │
│ └───────────────────────────────┘   │
│                                     │
│ ESPECIFICAÇÕES:                     │
│ Tecido: Poliéster - PP             │
│ Cor do Tecido: Branco              │
│ ...                                 │
└─────────────────────────────────────┘
```

## 💡 Dicas

- Se o navegador não baixar automaticamente, verifique as configurações de download
- Alguns visualizadores de PDF podem demorar um pouco para carregar imagens grandes
- O PDF gerado tem cerca de 20 KB, então é bem leve

## 🆘 Suporte

Se ainda tiver problemas:

1. Verifique o arquivo `CORRECAO_PDF_IMAGENS.md` para detalhes técnicos
2. Verifique o arquivo `SOLUCAO_FINAL_PDF.md` para entender a solução
3. Verifique os logs em `storage/logs/laravel.log`

---

**Boa sorte com o teste!** 🚀

