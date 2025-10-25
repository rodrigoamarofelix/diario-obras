# 🔍 Sistema de Busca Avançada

## Visão Geral

O Sistema de Busca Avançada permite aos usuários encontrar rapidamente informações em todo o sistema SGC - Gestão de Contratos. Oferece busca em tempo real, filtros múltiplos e resultados organizados por categoria.

## Funcionalidades Implementadas

### 1. **Busca Global**
- Busca simultânea em contratos, medições, pagamentos, pessoas e usuários
- Resultados organizados por tipo com ícones distintivos
- Links diretos para visualização dos itens encontrados

### 2. **Busca Específica por Categoria**
- **Contratos**: Número do contrato, objeto, número do processo
- **Medições**: Número da medição, observações, contrato relacionado
- **Pagamentos**: Número do pagamento, observações, medição relacionada
- **Pessoas**: Nome, CPF
- **Usuários**: Nome, email

### 3. **Filtros Avançados**
- **Status**: Ativo, Inativo, Pendente, Aprovado, Rejeitado
- **Período**: Data início e fim
- **Valores**: Valor mínimo e máximo
- **Usuário**: Filtro por usuário responsável
- **Lotação**: Filtro por lotação

### 4. **Recursos Adicionais**
- **Histórico de Buscas**: Últimas 10 buscas realizadas
- **Filtros Salvos**: Salvar e reutilizar combinações de filtros
- **Paginação**: Navegação eficiente em grandes volumes de resultados
- **Busca Rápida**: Componente no header para acesso imediato

## Componentes Implementados

### 1. **AdvancedSearchComponent**
- Componente principal de busca avançada
- Localização: `app/Livewire/AdvancedSearchComponent.php`
- View: `resources/views/livewire/advanced-search-component.blade.php`

### 2. **QuickSearchComponent**
- Componente de busca rápida no header
- Localização: `app/Livewire/QuickSearchComponent.php`
- View: `resources/views/livewire/quick-search-component.blade.php`

## Rotas Configuradas

```php
// Busca Avançada
Route::middleware('auth')->prefix('search')->name('search.')->group(function () {
    Route::get('/', function () {
        return view('search.index');
    })->name('index');
});
```

## Como Usar

### 1. **Acesso à Busca Avançada**
- Menu lateral: "Busca Avançada"
- URL: `/search`
- Barra de busca no header (busca rápida)

### 2. **Busca Básica**
1. Digite o termo de busca no campo principal
2. Selecione o tipo de busca (Tudo, Contratos, Medições, etc.)
3. Os resultados aparecerão automaticamente

### 3. **Busca com Filtros**
1. Clique em "Filtros" para expandir opções avançadas
2. Configure os filtros desejados
3. Os resultados serão filtrados em tempo real

### 4. **Salvar Filtros**
1. Configure os filtros desejados
2. Digite um nome para o filtro
3. Clique em "Salvar Filtro"
4. Reutilize clicando no nome do filtro salvo

## Exemplos de Uso

### Buscar Contrato por Número
```
Termo: "2024/001"
Tipo: Contratos
Resultado: Contrato específico com detalhes
```

### Buscar Medições por Período
```
Termo: (vazio)
Tipo: Medições
Filtros: Data início: 01/01/2024, Data fim: 31/12/2024
Resultado: Todas as medições do período
```

### Buscar Pagamentos por Valor
```
Termo: (vazio)
Tipo: Pagamentos
Filtros: Valor mínimo: 10000, Valor máximo: 50000
Resultado: Pagamentos no range de valores
```

## Performance

### Otimizações Implementadas
- **Livewire**: Atualizações em tempo real sem recarregar a página
- **Paginação**: Limitação de resultados por página
- **Índices**: Consultas otimizadas com relacionamentos
- **Cache**: Histórico e filtros salvos em sessão

### Limites de Resultados
- **Busca Global**: Máximo 5 resultados por categoria
- **Busca Específica**: Paginação de 10 itens por página
- **Busca Rápida**: Máximo 5 resultados totais

## Segurança

### Controle de Acesso
- **Autenticação**: Apenas usuários logados podem usar a busca
- **Autorização**: Respeita permissões de visualização por módulo
- **Sanitização**: Entradas de busca são sanitizadas automaticamente

### Proteção contra SQL Injection
- **Eloquent ORM**: Uso de consultas preparadas
- **Validação**: Validação de todos os parâmetros de entrada
- **Escape**: Escape automático de caracteres especiais

## Manutenção

### Logs de Busca
- Histórico de buscas salvo em sessão
- Possibilidade de implementar logs em banco de dados
- Métricas de uso podem ser coletadas

### Atualizações Futuras
- **Busca por Sinônimos**: Implementar busca por termos relacionados
- **Sugestões**: Auto-complete com sugestões inteligentes
- **Busca por Tags**: Sistema de tags para categorização
- **Exportação**: Exportar resultados de busca para PDF/Excel

## Troubleshooting

### Problemas Comuns

1. **Resultados não aparecem**
   - Verificar se há dados no banco
   - Confirmar permissões de acesso
   - Verificar logs de erro

2. **Busca muito lenta**
   - Verificar índices no banco de dados
   - Reduzir número de resultados por página
   - Otimizar consultas com relacionamentos

3. **Filtros não funcionam**
   - Verificar se os campos existem no banco
   - Confirmar tipos de dados corretos
   - Verificar validação de formulário

## Conclusão

O Sistema de Busca Avançada oferece uma experiência de usuário moderna e eficiente, permitindo encontrar informações rapidamente em todo o sistema. Com recursos como filtros múltiplos, histórico de buscas e busca em tempo real, melhora significativamente a produtividade dos usuários.




