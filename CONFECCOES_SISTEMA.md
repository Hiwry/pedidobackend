# Confecções Padrão do Sistema

## 📋 Tipos de Confecção Cadastrados

O sistema possui **6 tipos de confecção** cadastrados como padrão:

| ID  | Nome           | Ordem | Descrição                    |
|-----|----------------|-------|------------------------------|
| 100 | DTF            | 1     | Direct to Film               |
| 101 | SERIGRAFIA     | 2     | Serigrafia                   |
| 102 | BORDADO        | 3     | Bordado                      |
| 103 | EMBORRACHADO   | 4     | Emborrachado                 |
| 104 | SUB. LOCAL     | 5     | Sublimação Local/Parcial     |
| 105 | SUB. TOTAL     | 6     | Sublimação Total             |

## 🔧 Como Funciona

### 1. Tabela `product_options`
As confecções são armazenadas na tabela `product_options` com `type = 'personalizacao'`.

### 2. Relacionamentos com Tecidos
Cada confecção pode ter tecidos associados através da tabela `product_option_relations`:

**Algodão** está disponível para:
- DTF
- SERIGRAFIA
- BORDADO
- EMBORRACHADO

**Poliéster** está disponível para:
- DTF
- SERIGRAFIA
- SUB. LOCAL
- SUB. TOTAL

### 3. Tabela de Preços
A tabela `personalization_prices` armazena os preços por:
- Tipo de confecção
- Tamanho (A4, A3, etc.)
- Faixa de quantidade

## 📝 Modelo PersonalizationPrice

O modelo `PersonalizationPrice` possui métodos úteis:

```php
// Buscar preço específico
PersonalizationPrice::getPriceForPersonalization('DTF', 'A4', 50);

// Buscar tamanhos disponíveis
PersonalizationPrice::getSizesForType('SERIGRAFIA');

// Buscar faixas de preço
PersonalizationPrice::getPriceRangesForTypeAndSize('BORDADO', 'A4');

// Listar todos os tipos
PersonalizationPrice::getPersonalizationTypes();
```

## 🔄 Atualização dos Dados

Para atualizar as confecções no banco de dados:

```bash
php artisan db:seed --class=ProductOptionSeeder
```

## 📊 Estrutura de Dados

### Exemplo de Registro em `product_options`:
```json
{
  "id": 100,
  "type": "personalizacao",
  "name": "DTF",
  "price": 0,
  "parent_type": null,
  "parent_id": null,
  "active": true,
  "order": 1
}
```

### Exemplo de Preço em `personalization_prices`:
```json
{
  "personalization_type": "DTF",
  "size_name": "A4",
  "size_dimensions": "21x29.7cm",
  "quantity_from": 1,
  "quantity_to": 50,
  "price": 59.40,
  "active": true,
  "order": 0
}
```

## 🎨 Uso no Frontend

As confecções aparecem como opções de personalização nos formulários de pedidos. Ao selecionar uma confecção, o sistema:

1. Carrega os tecidos compatíveis
2. Carrega os tamanhos disponíveis
3. Calcula o preço baseado na quantidade
4. Permite upload de arquivos de arte

## 🔐 Permissões

- **Admin**: Pode gerenciar todas as confecções e preços
- **Usuário**: Pode visualizar e selecionar confecções nos pedidos

## 📈 Relatórios

O sistema pode gerar relatórios por tipo de confecção:
- Pedidos por confecção
- Faturamento por confecção
- Confecções mais utilizadas

---

**Última atualização:** 14/10/2025  
**Versão do Sistema:** Laravel 12.33.0

