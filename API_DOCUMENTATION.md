# 🔌 API REST Completa - SGC Gestão de Contratos

## 📋 Visão Geral

A API REST do SGC permite integração completa com sistemas externos, aplicações móveis e automação de processos. Todos os endpoints seguem padrões REST e retornam dados em formato JSON.

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação baseada em tokens.

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "master@test.com",
    "password": "password"
}
```

**Resposta:**
```json
{
    "success": true,
    "message": "Login realizado com sucesso",
    "data": {
        "user": {
            "id": 1,
            "name": "Master User",
            "email": "master@test.com",
            "profile": "master",
            "approval_status": "aprovado"
        },
        "token": "1|JoMhTKh2XF7Kz0wSnQ...",
        "token_type": "Bearer",
        "expires_at": "2025-11-15T20:58:32.000000Z"
    }
}
```

### Usar Token
```http
Authorization: Bearer SEU_TOKEN_AQUI
```

## 📊 Endpoints Disponíveis

### 🔐 Autenticação (`/api/auth/`)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/login` | Login e obtenção de token |
| `POST` | `/logout` | Logout (revogar token atual) |
| `POST` | `/logout-all` | Revogar todos os tokens |
| `GET` | `/me` | Informações do usuário autenticado |
| `POST` | `/refresh` | Renovar token |
| `GET` | `/tokens` | Listar tokens ativos |
| `DELETE` | `/tokens/{id}` | Revogar token específico |

### 📄 Contratos (`/api/contratos`)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/` | Listar contratos (com filtros) |
| `POST` | `/` | Criar novo contrato |
| `GET` | `/{id}` | Ver contrato específico |
| `PUT` | `/{id}` | Atualizar contrato |
| `DELETE` | `/{id}` | Excluir contrato |
| `GET` | `/stats` | Estatísticas de contratos |

**Filtros disponíveis:**
- `status` - Status do contrato (ativo, inativo, vencido, suspenso)
- `data_inicio` - Data de início (YYYY-MM-DD)
- `data_fim` - Data de fim (YYYY-MM-DD)
- `gestor_id` - ID do gestor
- `fiscal_id` - ID do fiscal
- `search` - Busca por número ou descrição
- `per_page` - Itens por página (padrão: 15)

**Exemplo de criação:**
```http
POST /api/contratos
Authorization: Bearer SEU_TOKEN_AQUI
Content-Type: application/json

{
    "numero": "CONTR-2025-001",
    "descricao": "Contrato de prestação de serviços",
    "data_inicio": "2025-01-01",
    "data_fim": "2025-12-31",
    "status": "ativo",
    "gestor_id": 1,
    "fiscal_id": 2
}
```

### 📊 Relatórios (`/api/relatorios/`)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/dashboard` | Dados do dashboard |
| `GET` | `/financeiro` | Relatório financeiro |
| `GET` | `/contratos` | Relatório de contratos |
| `GET` | `/medicoes` | Relatório de medições |
| `GET` | `/pagamentos` | Relatório de pagamentos |
| `GET` | `/usuarios` | Relatório de usuários |

**Exemplo de relatório financeiro:**
```http
GET /api/relatorios/financeiro?data_inicio=2025-01-01&data_fim=2025-12-31
Authorization: Bearer SEU_TOKEN_AQUI
```

### ℹ️ Informações do Sistema (`/api/system/`)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/info` | Informações do sistema |

## 🔧 Exemplos de Uso

### 1. Login e Listagem de Contratos

```bash
# 1. Fazer login
curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"master@test.com","password":"password"}'

# 2. Usar o token retornado para listar contratos
curl -X GET http://localhost:8000/api/contratos \
     -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 2. Criar Nova Medição

```bash
curl -X POST http://localhost:8000/api/medicoes \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Content-Type: application/json" \
     -d '{
         "contrato_id": 1,
         "catalogo_id": 1,
         "quantidade": 100,
         "valor_unitario": 50.00,
         "status": "pendente"
     }'
```

### 3. Obter Relatório Financeiro

```bash
curl -X GET "http://localhost:8000/api/relatorios/financeiro?data_inicio=2025-01-01&data_fim=2025-12-31" \
     -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## 📱 Casos de Uso

### 1. **App Mobile**
- Funcionários podem registrar medições pelo celular
- Consultar contratos e pagamentos em tempo real
- Notificações push para aprovações

### 2. **Integração ERP**
- Sistema de contabilidade importa pagamentos automaticamente
- Sincronização de dados entre sistemas
- Relatórios automatizados

### 3. **Dashboard Executivo**
- Power BI consome dados em tempo real
- Métricas atualizadas automaticamente
- KPIs personalizados

### 4. **Automação de Processos**
- Webhooks para notificações externas
- Integração com Slack/Teams
- Workflows automatizados

## 🛡️ Segurança

- **Autenticação obrigatória** para todos os endpoints (exceto login)
- **Rate Limiting** configurado
- **Validação de dados** em todas as requisições
- **Logs de acesso** para auditoria
- **Tokens com expiração** (30 dias)

## 📈 Rate Limits

- **Login:** 5 tentativas por minuto
- **API geral:** 100 requisições por minuto por usuário
- **Relatórios:** 20 requisições por minuto por usuário

## 🔍 Códigos de Status HTTP

| Código | Descrição |
|--------|-----------|
| `200` | Sucesso |
| `201` | Criado com sucesso |
| `400` | Requisição inválida |
| `401` | Não autenticado |
| `403` | Não autorizado |
| `404` | Não encontrado |
| `422` | Dados inválidos |
| `429` | Rate limit excedido |
| `500` | Erro interno do servidor |

## 📝 Formato de Resposta

Todas as respostas seguem o padrão:

```json
{
    "success": true|false,
    "message": "Mensagem descritiva",
    "data": { ... },
    "errors": { ... } // apenas em caso de erro
}
```

## 🚀 Testando a API

### Com cURL:
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"master@test.com","password":"password"}'

# Listar contratos
curl -X GET http://localhost:8000/api/contratos \
     -H "Authorization: Bearer SEU_TOKEN"
```

### Com Postman/Insomnia:
1. Importe a collection da API
2. Configure a autenticação Bearer Token
3. Teste os endpoints disponíveis

## 📞 Suporte

Para dúvidas sobre a API:
- Consulte esta documentação
- Verifique os logs do sistema
- Entre em contato com o administrador

---

**SGC - Gestão de Contratos v1.0.0**
*API REST Completa para Integração*




