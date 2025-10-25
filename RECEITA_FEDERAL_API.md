# Configuração da API da Receita Federal - CBC

## Como Configurar

### 1. Obter API Key
1. Acesse: https://www.gov.br/conecta/catalogo/apis/cadastro-base-do-cidadao-cbc-cpf/swagger_view
2. Faça o cadastro como desenvolvedor
3. Solicite acesso à API CBC
4. Obtenha sua API key

### 2. Configurar no .env
Adicione as seguintes variáveis no seu arquivo `.env`:

```env
# API Key da Receita Federal (obrigatório para produção)
RECEITA_FEDERAL_API_KEY=sua_api_key_aqui

# URL base da API (opcional - usa padrão se não informado)
RECEITA_FEDERAL_BASE_URL=https://api.cbc.gov.br/v1/cpf/
```

### 3. Modo de Desenvolvimento
Se não configurar a `RECEITA_FEDERAL_API_KEY`, o sistema automaticamente:
- Usa dados simulados para testes
- Permite testar com CPFs fictícios
- Mostra mensagem informativa na interface

### 4. CPFs de Teste (Modo Desenvolvimento)
- `111.444.777-35` → João Silva Santos
- `123.456.789-01` → Maria Oliveira Costa
- `987.654.321-00` → Pedro Souza Lima

## Funcionamento

### Com API Key Configurada
- Consulta real na Receita Federal
- Validação de situação cadastral
- Dados oficiais do governo

### Sem API Key (Desenvolvimento)
- Dados simulados para testes
- Validação matemática do CPF
- Interface funcional para desenvolvimento

## Documentação Oficial
- **Swagger**: https://www.gov.br/conecta/catalogo/apis/cadastro-base-do-cidadao-cbc-cpf/swagger_view
- **Portal Gov.br**: https://www.gov.br/conecta/
