# Sistema de Cadastro de Empresas

## 📋 Funcionalidades Implementadas

### ✅ Cadastro Completo de Empresas
- **Nome Fantasia** e **Razão Social**
- **CNPJ** com validação automática
- **Email** e **Site**
- **Telefone** e **WhatsApp** com máscaras
- **Endereço completo** com busca de CEP automática
- **Observações** adicionais
- **Status** ativo/inativo

### ✅ Validações Implementadas
- **CNPJ**: Validação completa com dígitos verificadores
- **Email**: Formato válido
- **CEP**: Busca automática via API ViaCEP
- **Campos obrigatórios**: Validação de preenchimento

### ✅ Busca de CEP Automática
- Integração com API ViaCEP (gratuita)
- Preenchimento automático de endereço, bairro, cidade e estado
- Campos de endereço ficam **readonly** após busca
- Validação de CEP com 8 dígitos

### ✅ Interface Moderna
- **CRUD completo** (Create, Read, Update, Delete)
- **Listagem** com pesquisa em tempo real
- **Visualização detalhada** com todas as informações
- **Edição** com formulário pré-preenchido
- **Status toggle** (ativar/inativar)
- **Máscaras** para CNPJ, telefone e CEP

### ✅ Funcionalidades Especiais
- **Formatação automática** de CNPJ, telefone e CEP
- **Links diretos** para WhatsApp e email
- **Pesquisa** por nome, CNPJ ou status
- **Paginação** para grandes volumes
- **Soft Delete** (exclusão lógica)

## 🚀 Como Usar

### 1. Acessar o Sistema
- Faça login no sistema
- No menu lateral, clique em **"Empresas"**
- O sistema está **fora do menu "Diário de Obras"**

### 2. Cadastrar Nova Empresa
- Clique em **"Nova Empresa"**
- Preencha os dados básicos (nome, razão social, CNPJ)
- Digite o **CEP** e clique em **"Buscar"**
- Os campos de endereço serão preenchidos automaticamente
- Adicione telefone, WhatsApp e observações
- Clique em **"Salvar Empresa"**

### 3. Buscar CEP
- Digite o CEP no formato: `00000-000`
- Clique no botão de busca (🔍)
- O sistema buscará automaticamente:
  - Endereço
  - Bairro
  - Cidade
  - Estado
- **Importante**: Estes campos ficam readonly após a busca

### 4. Validar CNPJ
- Digite o CNPJ no formato: `00.000.000/0000-00`
- Ao sair do campo, o sistema validará automaticamente
- Se inválido, mostrará alerta

## 📁 Estrutura de Arquivos

```
app/
├── Models/
│   └── Empresa.php                 # Model com validações e formatações
├── Http/Controllers/
│   └── EmpresaController.php       # Controller com CRUD completo
└── ...

database/
├── migrations/
│   └── 2024_01_15_000000_create_empresas_table.php
└── seeders/
    └── EmpresaSeeder.php           # Dados de exemplo

resources/views/empresas/
├── index.blade.php                 # Listagem
├── create.blade.php                # Cadastro
├── edit.blade.php                  # Edição
└── show.blade.php                  # Visualização

routes/
└── web.php                         # Rotas do sistema
```

## 🔧 Rotas Disponíveis

```php
// Rotas principais
GET    /empresas                    # Listagem
GET    /empresas/create             # Formulário de criação
POST   /empresas                    # Salvar nova empresa
GET    /empresas/{empresa}          # Visualizar empresa
GET    /empresas/{empresa}/edit     # Formulário de edição
PUT    /empresas/{empresa}          # Atualizar empresa
DELETE /empresas/{empresa}          # Excluir empresa

// Rotas especiais
GET    /empresas/{empresa}/toggle-status  # Ativar/Inativar
GET    /empresas/buscar-cep               # Buscar CEP
GET    /empresas/validar-cnpj            # Validar CNPJ
```

## 📊 Campos da Tabela

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | bigint | Chave primária |
| `nome` | string | Nome fantasia |
| `razao_social` | string | Razão social |
| `cnpj` | string(18) | CNPJ único |
| `email` | string | Email de contato |
| `telefone` | string(20) | Telefone |
| `whatsapp` | string(20) | WhatsApp |
| `cep` | string(10) | CEP |
| `endereco` | string | Endereço |
| `numero` | string(20) | Número |
| `complemento` | string | Complemento |
| `bairro` | string | Bairro |
| `cidade` | string | Cidade |
| `estado` | string(2) | Estado (UF) |
| `pais` | string(50) | País |
| `site` | string | Site da empresa |
| `observacoes` | text | Observações |
| `ativo` | boolean | Status ativo/inativo |
| `created_by` | bigint | ID do usuário criador |
| `created_at` | timestamp | Data de criação |
| `updated_at` | timestamp | Data de atualização |
| `deleted_at` | timestamp | Data de exclusão (soft delete) |

## 🎯 Próximas Melhorias Sugeridas

1. **Upload de Logo** da empresa
2. **Histórico de alterações** (auditoria)
3. **Exportação** para Excel/PDF
4. **Importação** em lote via CSV
5. **Integração** com Receita Federal para validação de CNPJ
6. **Relatórios** de empresas por região/status
7. **API REST** para integração externa

## 🔒 Segurança

- **Validação server-side** de todos os campos
- **Sanitização** de dados de entrada
- **Soft delete** para preservar histórico
- **Controle de acesso** por usuário autenticado
- **Validação de CNPJ** com algoritmo oficial

## 📱 Responsividade

- **Design responsivo** para mobile e desktop
- **Máscaras automáticas** em todos os campos
- **Interface touch-friendly** para dispositivos móveis
- **Validação em tempo real** para melhor UX

---

**Sistema desenvolvido com Laravel 10, Bootstrap 4 e AdminLTE 3**




