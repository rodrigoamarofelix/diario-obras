# Perfis do Sistema - Diário de Obras

## Perfis Sugeridos para o Sistema Completo

### 1. **master** - Super Administrador
**Permissões:**
- ✅ Acesso total ao sistema
- ✅ Criar, editar e excluir qualquer usuário (soft delete)
- ✅ Gerenciar backups e auditoria
- ✅ Configurações gerais do sistema
- ✅ Exportação de dados
- ✅ Gerenciar todos os módulos do Diário de Obras

### 2. **admin** - Administrador
**Permissões:**
- ✅ Gerenciar contratos, medições e pagamentos
- ✅ Cadastrar e editar catálogos e lotações
- ✅ Gerenciar pessoas e empresas
- ✅ Acesso a relatórios e exportações
- ✅ Aprovar workflows
- ✅ Gerenciar módulos do Diário de Obras
- ✅ Cadastrar projetos, equipes e atividades
- ✅ Visualizar e gerenciar fotos e materiais
- ❌ Não pode gerenciar usuários
- ❌ Não pode acessar backups

### 3. **gestor** - Gestor de Contratos
**Permissões:**
- ✅ Visualizar e gerenciar contratos atribuídos
- ✅ Gerenciar responsáveis de contratos
- ✅ Visualizar medições e pagamentos
- ✅ Gerar relatórios financeiros
- ✅ Editar informações de pessoas (vinculadas aos contratos)
- ✅ Visualizar lotações
- ✅ Aprovar medições (se configurado no workflow)
- ✅ Exportar dados dos contratos
- ✅ Visualizar Dashboard do Diário de Obras
- ✅ Visualizar projetos, equipes e atividades
- ❌ Não pode cadastrar novos catálogos
- ❌ Não pode excluir contratos permanentes
- ❌ Não pode gerenciar usuários

### 4. **fiscal** - Fiscal de Obra
**Permissões:**
- ✅ Registrar medições
- ✅ Registrar atividades de obra
- ✅ Registrar fotos da obra
- ✅ Cadastrar materiais utilizados
- ✅ Gerenciar equipes de obra
- ✅ Visualizar contratos atribuídos
- ✅ Consultar catálogos e lotações
- ✅ Visualizar relatórios de progresso
- ✅ Dashboard do Diário de Obras (registro)
- ✅ Visualizar seus próprios projetos atribuídos
- ❌ Não pode aprovar nada
- ❌ Não pode ver dados financeiros completos
- ❌ Não pode exportar dados

### 5. **construtor** - Construtor/Fornecedor (NOVO)
**Permissões:**
- ✅ Visualizar seus contratos
- ✅ Registrar medições dos seus contratos
- ✅ Anexar documentos e fotos
- ✅ Atualizar status das atividades
- ✅ Consultar pagamentos recebidos
- ✅ Visualizar suas equipes
- ✅ Dashboard do Diário de Obras (consulta)
- ❌ Não pode ver outros contratos
- ❌ Não pode ver dados de outros fornecedores

### 6. **visualizador** - Visualizador/Consultor
**Permissões:**
- ✅ Visualizar todos os contratos (somente leitura)
- ✅ Visualizar medições e pagamentos (somente leitura)
- ✅ Gerar relatórios
- ✅ Exportar dados para análise
- ✅ Dashboard com estatísticas
- ✅ Busca avançada
- ✅ Visualizar documentos públicos
- ✅ Visualizar Dashboards do Diário de Obras
- ❌ Não pode criar, editar ou excluir nada
- ❌ Não pode ver dados sensíveis (senhas, códigos)

### 7. **user** - Usuário Básico
**Permissões:**
- ✅ Visualizar seu próprio perfil
- ✅ Atualizar suas informações pessoais
- ✅ Alterar senha
- ✅ Visualizar notificações
- ✅ Consultar contratos públicos
- ✅ Dashboard com informações básicas
- ❌ Não pode ver dados financeiros
- ❌ Não pode exportar dados
- ❌ Não tem acesso ao Diário de Obras

---

## Resumo por Módulo

### Módulo: Administração
- **master**: ✅ Total acesso
- **admin**: ✅ Gestão de dados
- **gestor**: ❌ Sem acesso
- **fiscal**: ❌ Sem acesso
- **construtor**: ❌ Sem acesso
- **visualizador**: ❌ Sem acesso
- **user**: ❌ Sem acesso

### Módulo: Parametrização
- **master**: ✅ Total acesso
- **admin**: ✅ Gerenciar tudo
- **gestor**: ✅ Visualizar e editar relacionado aos contratos
- **fiscal**: ✅ Consultar catálogos e lotações
- **construtor**: ✅ Consultar catálogos
- **visualizador**: ✅ Somente leitura
- **user**: ❌ Sem acesso

### Módulo: Diário de Obras
- **master**: ✅ Total acesso
- **admin**: ✅ Total gestão
- **gestor**: ✅ Visualizar e aprovar
- **fiscal**: ✅ Registrar atividades
- **construtor**: ✅ Registrar suas atividades
- **visualizador**: ✅ Somente visualização
- **user**: ❌ Sem acesso

### Módulo: Exportação e Relatórios
- **master**: ✅ Todos os relatórios
- **admin**: ✅ Relatórios gerenciais
- **gestor**: ✅ Relatórios financeiros dos seus contratos
- **fiscal**: ✅ Relatórios de progresso
- **construtor**: ✅ Relatórios dos seus contratos
- **visualizador**: ✅ Exportação de dados públicos
- **user**: ❌ Sem acesso

---

## Hieraquia de Permissões

```
master (Super Admin)
├── admin (Administrador)
│   ├── gestor (Gestor de Contratos)
│   ├── fiscal (Fiscal de Obra)
│   ├── construtor (Fornecedor)
│   └── visualizador (Consultor)
└── user (Usuário Básico)
```

