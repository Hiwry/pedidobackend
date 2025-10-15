# 🔧 Como Habilitar a Extensão PHP GD no XAMPP

## ❗ Erro Atual
```
The PHP GD extension is required, but is not installed.
```

## ✅ Solução Rápida

### Passo 1: Editar php.ini
1. Abra o arquivo: `C:\xampp\php\php.ini`
2. Procure por: `;extension=gd`
3. **Remova o ponto e vírgula** (`;`) do início da linha:

**ANTES:**
```ini
;extension=gd
```

**DEPOIS:**
```ini
extension=gd
```

### Passo 2: Reiniciar Apache
1. Abra o **XAMPP Control Panel**
2. Clique em **Stop** no Apache
3. Clique em **Start** no Apache

### Passo 3: Verificar
Execute no terminal:
```bash
php -m | findstr gd
```

Deve aparecer: `gd`

## 🎯 Alternativa: Usar cmd do XAMPP

Se não quiser habilitar GD, posso modificar o código para NÃO redimensionar imagens (mas o PDF pode ficar mais pesado).

## 📝 Nota
A extensão GD é necessária para:
- Redimensionar imagens antes de colocar no PDF
- Otimizar o tamanho do PDF
- Processar PNG/JPG/GIF

