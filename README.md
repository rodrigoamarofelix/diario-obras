# 🏗️ Sistema de Diário de Obras

Sistema completo para gerenciamento de obras, equipes, atividades e documentos para empresas de construção civil.

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Estrutura de Módulos](#estrutura-de-módulos)
- [Guia de Uso](#guia-de-uso)
- [Versões](#versões)

## 🎯 Sobre o Projeto

O **Sistema de Diário de Obras** foi desenvolvido para gerenciar todos os aspectos de uma obra, desde o registro de equipes até o controle de pagamentos e materiais. O sistema oferece uma interface completa para gestão de projetos de construção civil, com funcionalidades avançadas de auditoria, relatórios e exportação de dados.

### Objetivo Principal
Centralizar e automatizar o gerenciamento de obras, proporcionando:
- Controle total de equipes e atividades
- Registro detalhado do dia a dia da obra
- Gestão de materiais e pagamentos
- Sistema de auditoria completo
- Relatórios personalizados

## ✨ Funcionalidades

### 🏢 Módulo Administrativo
- **Usuários**: Gerenciamento completo de usuários do sistema
- **Auditoria**: Rastreamento de todas as operações realizadas
- **Backup**: Sistema de backup automático de dados
- **Autenticação 2FA**: Segurança com dois fatores
- **Perfil**: Gerenciamento de perfil do usuário

### ⚙️ Parametrização
- **Funções**: Cadastro de funções/cargos
- **Empresas**: Gestão de empresas
- **Lotações**: Controle de lotações
- **Pessoas**: Cadastro de pessoas
- **Contratos**: Gestão de contratos
- **Catálogos**: Sistema de catálogos
- **Medições**: Registro de medições
- **Pagamentos**: Controle de pagamentos
- **Relatórios**: Geração de relatórios
- **Exportação**: Exportação de dados
- **Busca Avançada**: Sistema de busca
- **Workflow**: Gestão de workflow

### 📝 Diário de Obras
- **Projetos**: Gestão de projetos de obra
- **Equipes**: Registro de equipes de trabalho
- **Atividades**: Criação e acompanhamento de atividades
- **Materiais**: Controle de materiais
- **Fotos**: Sistema de fotos da obra
- **Relatórios Detalhados**: Relatórios completos

## 🛠️ Tecnologias

### Backend
- **Laravel 10**: Framework PHP
- **PHP 8.1+**: Linguagem de programação
- **PostgreSQL**: Banco de dados
- **Eloquent ORM**: ORM do Laravel

### Frontend
- **Blade Templates**: Sistema de templates
- **AdminLTE 3**: Interface administrativa
- **Bootstrap 4**: Framework CSS
- **jQuery**: Biblioteca JavaScript
- **Font Awesome**: Ícones
- **Select2**: Seleção avançada
- **Chart.js**: Gráficos

### Segurança
- **Autenticação Multi-fator (2FA)**
- **Auditoria Completa**
- **Soft Deletes**
- **Backup Automático**

## 📦 Requisitos

- PHP >= 8.1
- Composer
- PostgreSQL >= 13
- Node.js e NPM
- Git

## 🚀 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/rodrigoamarofelix/diario-obras.git
cd diario-obras
```

### 2. Instale as dependências
```bash
composer install
npm install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados
Edite o arquivo `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=diario_obras
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Execute as migrations
```bash
php artisan migrate
php artisan db:seed
```

### 6. Compile os assets
```bash
npm run build
```

### 7. Inicie o servidor
```bash
php artisan serve
```

## 📂 Estrutura de Módulos

### Módulo Administrativo
```
📁 app/Http/Controllers/
├── UserController.php - Gerenciamento de usuários
├── AuditoriaController.php - Sistema de auditoria
├── BackupController.php - Sistema de backup
└── TwoFactorController.php - Autenticação 2FA
```

### Módulo de Parametrização
```
📁 app/Http/Controllers/
├── FuncaoController.php - Funções
├── EmpresaController.php - Empresas
├── LotacaoController.php - Lotações
├── PessoaController.php - Pessoas
├── ContratoController.php - Contratos
├── CatalogoController.php - Catálogos
├── MedicaoController.php - Medições
└── PagamentoController.php - Pagamentos
```

### Módulo Diário de Obras
```
📁 app/Http/Controllers/
├── ProjetoController.php - Projetos
├── EquipeObraController.php - Equipes
├── AtividadeObraController.php - Atividades
├── MaterialObraController.php - Materiais
└── FotoObraController.php - Fotos
```

## 📖 Guia de Uso

### Cadastrando um Projeto
1. Acesse **Diário de Obras > Projetos**
2. Clique em **Novo Projeto**
3. Preencha os dados do projeto
4. Salve

### Registrando Equipe
1. Acesse **Diário de Obras > Equipe**
2. Clique em **Registrar Equipe**
3. Selecione o projeto
4. Adicione os funcionários
5. Informe horários e atividades
6. Salve

### Criando uma Atividade
1. Acesse **Diário de Obras > Atividades**
2. Clique em **Nova Atividade**
3. Selecione projeto, data e responsável
4. Defina tipo e status
5. Salve

### Visualizando Relatórios
1. Acesse **Diário de Obras > Relatórios**
2. Selecione o tipo de relatório
3. Configure filtros
4. Gere o relatório

## 🎨 Recursos Avançados

### Cálculo Automático de Horas
O sistema calcula automaticamente as horas trabalhadas pela equipe:
- **Horas da Manhã**: Entrada até saída para almoço
- **Horas da Tarde**: Retorno do almoço até saída
- **Total**: Soma das horas trabalhadas

### Sistema de Agrupamento
As equipes são agrupadas automaticamente por:
- Projeto
- Data
- Exibição em cards expansíveis

### Auditoria Completa
Todas as ações são auditadas:
- Criação de registros
- Edição de dados
- Exclusão (soft delete)
- Alterações de status

## 📊 Relatórios Disponíveis

- **Relatório de Equipe**: Detalhes da equipe por período
- **Relatório de Atividades**: Atividades realizadas
- **Relatório de Materiais**: Controle de materiais
- **Relatório de Pagamentos**: Controle financeiro
- **Relatório Detalhado**: Visão completa da obra

## 🔒 Segurança

- Autenticação requerida em todas as páginas
- Níveis de permissão por usuário
- Autenticação 2FA disponível
- Soft delete para recuperação de dados
- Auditoria de todas as operações

## 📝 Notas de Versão

### Versão 1.0.0 Estável (v1.0.0-estavel)
**Data**: 26/10/2025

#### Correções Implementadas
- ✅ Corrigido relacionamento de responsável em Atividades
- ✅ Corrigido foreign key constraints no PostgreSQL
- ✅ Adicionado botão Voltar em todas as telas
- ✅ Cálculo automático de horas trabalhadas (manhã/tarde)
- ✅ Agrupamento inteligente de equipes
- ✅ Exibição correta de responsáveis em listagens
- ✅ Cálculo automático de tempo gasto em atividades
- ✅ Preenchimento correto de campos hora em edição
- ✅ Ajuste de alinhamento de ícones
- ✅ Correções de sequência PostgreSQL em todos os módulos
- ✅ Validações e formatos de data/hora corrigidos
- ✅ Melhoria na experiência do usuário em todos os módulos

#### Funcionalidades Novas
- 🌞 Cálculo de horas da manhã e tarde
- 👥 Agrupamento de equipes por projeto e data
- 🔄 Botões de navegação melhorados
- 📊 Campos calculados automaticamente

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:
1. Fazer Fork do projeto
2. Criar uma branch para sua feature
3. Fazer commit das mudanças
4. Fazer Push para a branch
5. Abrir um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Rodrigo Amaral Felix**
- GitHub: [@rodrigoamarofelix](https://github.com/rodrigoamarofelix)

## 📞 Suporte

Para suporte, envie um email para suporte@exemplo.com ou abra uma issue no GitHub.

---

**Desenvolvido com ❤️ usando Laravel**
