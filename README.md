# Sistema de Pedidos - Confecções

Sistema completo de gestão de pedidos para confecções, desenvolvido em Laravel com funcionalidades avançadas de edição, logs e geração de PDFs.

## 🚀 Funcionalidades

### 📋 Gestão de Pedidos
- **Criação de pedidos** com wizard passo-a-passo
- **Edição completa de pedidos** com sistema de aprovação
- **Histórico detalhado** de todas as alterações
- **Sistema de logs** para auditoria completa

### 👥 Gestão de Clientes
- Cadastro completo de clientes
- Histórico de pedidos por cliente
- Dados de contato e endereçamento

### 🎨 Personalização
- **Serigrafia** e **DTF** (Direct to Film)
- **Sublimação** com localização e tamanhos
- **Preços personalizados** por tipo de aplicação
- **Upload de imagens** com otimização automática

### 📊 Relatórios e Downloads
- **PDF de Costura** (A4) com otimização de imagens
- **PDF de Personalização** com todas as especificações
- **Nota do Cliente** em PDF
- **Sistema de compartilhamento** de pedidos

### 🔧 Sistema de Edição Avançado
- **Edição direta** para administradores
- **Sistema de aprovação** para usuários normais
- **Logs detalhados** de todas as alterações
- **Histórico visual** com interface interativa

## 🛠️ Tecnologias Utilizadas

- **Laravel 10** - Framework PHP
- **MySQL** - Banco de dados
- **DomPDF** - Geração de PDFs
- **GD Extension** - Processamento de imagens
- **Tailwind CSS** - Interface moderna
- **JavaScript** - Interatividade

## 📦 Instalação

### Pré-requisitos
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js (opcional)

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/sistema-pedidos-confeccoes.git
cd sistema-pedidos-confeccoes
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```bash
# Edite o arquivo .env com suas credenciais do MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrações**
```bash
php artisan migrate
php artisan db:seed
```

6. **Configure o storage**
```bash
php artisan storage:link
```

7. **Inicie o servidor**
```bash
php artisan serve
```

## 🔐 Usuários Padrão

Após executar as seeds, você terá:
- **Administrador**: admin@admin.com / senha: admin
- **Vendedor**: vendedor@vendedor.com / senha: vendedor

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── Helpers/             # Classes auxiliares
├── database/
│   ├── migrations/          # Migrações do banco
│   └── seeders/            # Seeds para dados iniciais
├── resources/
│   ├── views/              # Views Blade
│   └── css/                # Estilos
├── routes/
│   └── web.php             # Rotas da aplicação
└── storage/
    ├── app/public/         # Arquivos públicos
    └── logs/               # Logs da aplicação
```

## 🎯 Funcionalidades Principais

### Sistema de Edição de Pedidos
- **Wizard de edição** passo-a-passo
- **Aplicação direta** para administradores
- **Sistema de aprovação** para usuários normais
- **Logs detalhados** de todas as alterações

### Geração de PDFs
- **Otimização automática** de imagens grandes
- **Suporte a múltiplos formatos** (JPG, PNG)
- **Compressão inteligente** para melhor performance
- **Tratamento de erros** robusto

### Sistema de Logs
- **Histórico completo** de alterações
- **Interface visual** para visualização
- **Detalhamento** de mudanças (antes → depois)
- **Auditoria completa** do sistema

## 🔧 Configurações Avançadas

### Otimização de Imagens
O sistema otimiza automaticamente imagens maiores que 1MB:
- Redimensiona para máximo 800x600px
- Compressão de 85% para JPG
- Compressão de 8 para PNG
- Preserva transparência

### Sistema de Cache
- Cache de configurações
- Cache de views
- Cache de rotas
- Limpeza automática

## 📝 Logs e Debug

Todos os logs são salvos em `storage/logs/laravel.log`:
- Edições de pedidos
- Geração de PDFs
- Erros e exceções
- Performance e otimizações

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte ou dúvidas:
- Abra uma issue no GitHub
- Entre em contato via email
- Consulte a documentação

---

**Desenvolvido com ❤️ para confecções**