# Como Usar os Perfis no Sistema

## Perfis Implementados

1. **master** - Super Administrador (total acesso)
2. **admin** - Administrador (gestão operacional)
3. **gestor** - Gestor de Contratos
4. **fiscal** - Fiscal de Obra
5. **construtor** - Construtor/Fornecedor
6. **visualizador** - Visualizador/Consultor
7. **user** - Usuário Básico

## Métodos Disponíveis no Modelo User

### Verificação de Perfil
```php
$user->isMaster()
$user->isAdmin()
$user->isGestor()
$user->isFiscal()
$user->isConstrutor()
$user->isVisualizador()
$user->isUser()
```

### Verificação de Permissões por Módulo
```php
$user->canAccessAdministration()      // Acesso ao módulo Administração
$user->canAccessParametrizacao()      // Acesso ao módulo Parametrização
$user->canAccessDiarioObras()         // Acesso ao módulo Diário de Obras
$user->canEdit()                       // Pode editar (não apenas visualizar)
$user->canOnlyView()                   // Apenas visualização
$user->canExport()                     // Pode exportar dados
```

### Verificação de Gerenciamento de Usuários
```php
$user->canManageUsers()    // Pode gerenciar usuários
$user->canDeleteUsers()    // Pode excluir usuários (apenas master)
```

## Como Usar nos Controllers

### Opção 1: Verificação Manual
```php
public function index()
{
    if (!Auth::user()->canAccessParametrizacao()) {
        abort(403, 'Você não tem permissão para acessar este módulo.');
    }

    // Sua lógica aqui
}
```

### Opção 2: Middleware nas Rotas
```php
// Apenas master e admin
Route::middleware(['profile:master,admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});

// Master, admin e gestor
Route::middleware(['profile:master,admin,gestor'])->group(function () {
    Route::get('/contratos', [ContratoController::class, 'index']);
});
```

## Como Usar nas Views (Blade)

### Exibir/Ocultar Elementos
```blade
@if(Auth::user()->canEdit())
    <button type="submit" class="btn btn-primary">Editar</button>
@endif

@if(Auth::user()->canExport())
    <a href="{{ route('exports.index') }}">Exportar</a>
@endif

@if(Auth::user()->isMaster())
    <div class="alert alert-warning">
        Apenas master pode ver isso
    </div>
@endif
```

## Exemplos de Implementação

### Restringir Exportação
```php
// app/Http/Controllers/ExportController.php

public function index()
{
    if (!Auth::user()->canExport()) {
        return redirect()->back()->with('error', 'Você não tem permissão para exportar dados.');
    }

    return view('exports.index');
}
```

### Restringir Acesso ao Menu
```blade
{{-- resources/views/layouts/admin.blade.php --}}

@if(Auth::user()->canAccessDiarioObras())
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-clipboard-list text-warning"></i>
            <p>Diário de Obras</p>
        </a>
    </li>
@endif
```

### Filtrar Dados por Perfil
```php
// Mostrar apenas contratos atribuídos ao gestor
if (Auth::user()->isGestor()) {
    $contratos = Contrato::where('gestor_id', Auth::id())->get();
} else {
    $contratos = Contrato::all();
}
```

## Implementações Pendentes

Para completar a implementação dos perfis, é necessário:

1. ✅ Adicionar métodos de verificação no modelo User
2. ✅ Adicionar novos perfis às validações
3. ✅ Atualizar formulários de criação/edição
4. ⏳ Adicionar verificações nos controllers
5. ⏳ Adicionar restrições no menu (sidebar)
6. ⏳ Implementar filtros de dados por perfil
7. ⏳ Criar testes de permissões

### Próximos Passos

1. Adicionar verificação `canAccessAdministration()` no `UserController`
2. Adicionar verificação `canAccessDiarioObras()` nos controllers de Diário de Obras
3. Adicionar verificação `canEdit()` nos formulários de criação
4. Implementar filtros de dados por perfil em controllers que listam dados

