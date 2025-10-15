# 🎨 Guia de Múltiplas Aplicações por Item

## ✅ Funcionalidades Implementadas

### **1. Múltiplas Aplicações por Item**
Agora você pode adicionar **quantas aplicações quiser** para cada tipo de personalização!

**Exemplos:**
- Item 1 - DTF: 2 aplicações (frente + costas)
- Item 1 - SERIGRAFIA: 3 aplicações (frente + manga direita + manga esquerda)
- Item 2 - BORDADO: 5 aplicações (peito + costas + 2 mangas + gola)

### **2. Resumo de Valores Completo**
Sistema calcula automaticamente todos os valores:
- Valor das peças
- Valor de cada aplicação  
- Total por item
- Total geral do pedido

### **3. Capa Individual por Item + Personalização**
Cada combinação tem sua própria capa com Drag & Drop e Ctrl+V

---

## 🎯 Como Usar

### **Passo 1: Selecionar Aba**
Escolha qual Item + Personalização você quer configurar

### **Passo 2: Upload da Capa**
- Arraste a imagem
- Ou clique para selecionar
- Ou Ctrl+V para colar

### **Passo 3: Adicionar Aplicações**

#### **Para DTF:**
1. Selecione o tamanho (A4, A3, 10x15cm)
2. Escolha a localização (Frente, Costas, Manga)
3. Clique em "➕ Adicionar Aplicação DTF"
4. Repita para adicionar mais aplicações

#### **Para BORDADO:**
1. Selecione o tamanho (5x5, 10x10, 15x15cm)
2. Digite as cores separadas por vírgula
3. Escolha a localização
4. Clique em "➕ Adicionar Bordado"
5. Repita para adicionar mais bordados

#### **Para SERIGRAFIA:**
1. Selecione o tamanho (A4, A3, 20x30cm)
2. Digite as cores (cada cor = 1 fotolito)
3. Escolha a localização
4. Clique em "➕ Adicionar Serigrafia"
5. Repita para adicionar mais serigrafias

#### **Para SUBLIMAÇÃO:**
1. Selecione o tamanho (A4, A3, 20x30cm)
2. Escolha a localização
3. Clique em "➕ Adicionar Aplicação"
4. Repita conforme necessário

---

## 💰 Sistema de Cálculo Automático

### **Preços Padrão:**

#### **DTF:**
- 10x15cm: R$ 8,00
- A4: R$ 12,00
- A3: R$ 20,00

#### **BORDADO:**
- 5x5cm: R$ 8,00
- 10x10cm: R$ 15,00
- 15x15cm: R$ 25,00

#### **SERIGRAFIA:**
- 20x30cm: R$ 12,00
- A4: R$ 10,00
- A3: R$ 18,00

#### **SUBLIMAÇÃO:**
- 20x30cm: R$ 18,00
- A4: R$ 15,00
- A3: R$ 25,00

### **Cálculo por Aplicação:**
```
Valor da Aplicação × Quantidade de Peças = Total da Aplicação
```

**Exemplo:**
- DTF A4 = R$ 12,00
- Quantidade = 50 peças
- **Total = R$ 600,00**

---

## 📊 Resumo de Valores

### **Por Item:**
```
┌────────────────────────────────────┐
│ 💰 Resumo de Valores - Item 1     │
├────────────────────────────────────┤
│ Valor da Peça:      R$ 25,00      │
│ Quantidade:         50 peças       │
│ Subtotal Peças:     R$ 1.250,00   │
│ Total de Aplicações: 2             │
│ Valor das Aplicações: R$ 600,00   │
├════════════════════════════════════┤
│ TOTAL ITEM 1:       R$ 1.850,00   │
└────────────────────────────────────┘
```

### **Geral do Pedido:**
```
┌────────────────────────────────────┐
│ 💰 Resumo Geral do Pedido         │
├────────────────────────────────────┤
│ Total de Itens:     3              │
│ Total de Peças:     150 peças      │
│ Subtotal (Peças):   R$ 3.750,00   │
│ Total de Aplicações: 8             │
│ Valor das Aplicações: R$ 1.800,00 │
├════════════════════════════════════┤
│ TOTAL DO PEDIDO:    R$ 5.550,00   │
└────────────────────────────────────┘
```

---

## 🎨 Interface Visual

### **Lista de Aplicações:**
```
┌──────────────────────────────────────┐
│ Aplicação 1                      [✕] │
│ Tamanho: A4 - R$ 12,00              │
│ Localização: Frente                  │
│ R$ 12,00 × 50 peças = R$ 600,00     │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ Aplicação 2                      [✕] │
│ Tamanho: 10x15cm - R$ 8,00          │
│ Localização: Costas                  │
│ R$ 8,00 × 50 peças = R$ 400,00      │
└──────────────────────────────────────┘
```

### **Botão de Remover:**
Clique no **[✕]** para remover qualquer aplicação

---

## 💡 Exemplos Práticos

### **Exemplo 1: Uniforme Completo (DTF)**

**Item 1 - DTF - 50 peças:**

**Aplicação 1:** Logo grande na frente
- Tamanho: A4 (R$ 12,00)
- Localização: Frente
- Total: R$ 600,00

**Aplicação 2:** Nome nas costas
- Tamanho: 10x15cm (R$ 8,00)
- Localização: Costas
- Total: R$ 400,00

**Aplicação 3:** Número na manga
- Tamanho: 10x15cm (R$ 8,00)
- Localização: Manga Direita
- Total: R$ 400,00

**Total do Item:** R$ 1.250,00 (peças) + R$ 1.400,00 (aplicações) = **R$ 2.650,00**

---

### **Exemplo 2: Camisa Social (BORDADO)**

**Item 2 - BORDADO - 30 peças:**

**Aplicação 1:** Logo pequeno no peito
- Tamanho: 5x5cm (R$ 8,00)
- Cores: Azul, Branco
- Localização: Peito Esquerdo
- Total: R$ 240,00

**Aplicação 2:** Nome na manga
- Tamanho: 10x10cm (R$ 15,00)
- Cores: Azul
- Localização: Manga Direita
- Total: R$ 450,00

**Aplicação 3:** Iniciais na gola
- Tamanho: 5x5cm (R$ 8,00)
- Cores: Azul
- Localização: Gola
- Total: R$ 240,00

**Total do Item:** R$ 900,00 (peças) + R$ 930,00 (bordados) = **R$ 1.830,00**

---

### **Exemplo 3: Promoção (SERIGRAFIA)**

**Item 3 - SERIGRAFIA - 100 peças:**

**Aplicação 1:** Logo frente (2 cores)
- Tamanho: A4 (R$ 10,00)
- Cores: Branco, Preto
- Localização: Frente
- Total: R$ 1.000,00

**Aplicação 2:** Frase costas (1 cor)
- Tamanho: 20x30cm (R$ 12,00)
- Cores: Branco
- Localização: Costas
- Total: R$ 1.200,00

**Aplicação 3:** Marca manga (1 cor)
- Tamanho: A4 (R$ 10,00)
- Cores: Preto
- Localização: Manga
- Total: R$ 1.000,00

**Total do Item:** R$ 1.500,00 (peças) + R$ 3.200,00 (serigrafias) = **R$ 4.700,00**

---

## 🔥 Recursos Avançados

### **1. Adicionar/Remover Aplicações:**
- ✅ Adicione quantas aplicações precisar
- ✅ Remova aplicações com um clique
- ✅ Cálculo automático em tempo real

### **2. Validações:**
- ✅ Não permite adicionar sem preencher campos
- ✅ Alerta se faltar informação
- ✅ Preços sempre corretos

### **3. Preview de Aplicações:**
- ✅ Lista visual de todas as aplicações
- ✅ Detalhes completos de cada uma
- ✅ Valor individual e total

### **4. Resumo Dinâmico:**
- ✅ Atualização instantânea dos valores
- ✅ Total por item
- ✅ Total geral do pedido

---

## 📋 Fluxo Completo

```
1. SELECIONAR ABA
   └── Item 1: DTF

2. FAZER UPLOAD DA CAPA
   └── Drag & Drop ou Ctrl+V

3. ADICIONAR APLICAÇÕES
   ├── Aplicação 1: A4 - Frente
   ├── Aplicação 2: 10x15 - Costas
   └── Aplicação 3: 10x15 - Manga

4. REVISAR RESUMO
   ├── 3 aplicações adicionadas
   ├── Valor: R$ 1.400,00
   └── Total Item: R$ 2.650,00

5. IR PARA PRÓXIMA ABA
   └── Item 1: SERIGRAFIA

6. REPETIR PROCESSO

7. REVISAR RESUMO GERAL
   └── Total Pedido: R$ 5.550,00

8. SALVAR E CONTINUAR
```

---

## 🎯 Dicas Pro

### **Organização:**
1. Configure todas as aplicações de um item antes de ir para o próximo
2. Use nomes descritivos nas cores (ex: "Azul Royal, Branco Gelo")
3. Revise o resumo antes de salvar

### **Economia:**
1. Agrupe aplicações do mesmo tamanho quando possível
2. Para SERIGRAFIA, menos cores = menos custo
3. Planeje localizações para otimizar produção

### **Qualidade:**
1. Upload de capas em alta resolução
2. Especifique cores exatas do bordado
3. Indique localizações precisas

---

## 🐛 Solução de Problemas

### **"Não consigo adicionar aplicação"**
- ✔️ Preencha todos os campos obrigatórios
- ✔️ Verifique se selecionou tamanho e localização
- ✔️ Para BORDADO/SERIGRAFIA, preencha as cores

### **"Valor não atualiza"**
- ✔️ Aguarde alguns segundos após adicionar
- ✔️ Verifique se a aplicação foi adicionada na lista
- ✔️ Recarregue a página se necessário

### **"Removi aplicação por engano"**
- ✔️ Adicione novamente com os mesmos dados
- ✔️ Os valores serão recalculados automaticamente

---

## 📊 Comparação: Antes vs Agora

### **Antes:**
```
❌ 1 aplicação por item
❌ Sem cálculo de valores
❌ Sem resumo detalhado
❌ Sem preview de aplicações
```

### **Agora:**
```
✅ MÚLTIPLAS aplicações por item
✅ Cálculo AUTOMÁTICO de valores
✅ Resumo COMPLETO e detalhado
✅ Preview VISUAL de aplicações
✅ Drag & Drop e Ctrl+V
✅ Remoção fácil de aplicações
```

---

## 🎉 Vantagens

1. **Flexibilidade Total:** Adicione quantas aplicações precisar
2. **Transparência:** Veja exatamente quanto cada aplicação custa
3. **Organização:** Lista visual de todas as aplicações
4. **Velocidade:** Calcule valores instantaneamente
5. **Precisão:** Valores sempre corretos e atualizados
6. **Controle:** Adicione/remova aplicações livremente

---

## 💪 Casos de Uso

### **Uniformes Esportivos:**
- Frente: Logo do time (R$ 600)
- Costas: Nome + Número (R$ 800)
- Mangas: Patrocinadores (R$ 400)
**Total:** R$ 1.800 em aplicações

### **Camisas Empresariais:**
- Peito: Logo pequeno (R$ 240)
- Manga: Nome da empresa (R$ 450)
- Costas: Slogan (R$ 600)
**Total:** R$ 1.290 em aplicações

### **Eventos Promocionais:**
- Frente: Arte grande (R$ 1.000)
- Costas: Lista de patrocinadores (R$ 1.200)
- Manga: Marca do evento (R$ 500)
**Total:** R$ 2.700 em aplicações

---

**Sistema pronto para maximizar sua produtividade e precisão!** 🚀✨

