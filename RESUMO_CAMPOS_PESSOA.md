# ✅ Campos Adicionados ao Módulo de Pessoas

## 📋 Resumo das Alterações

### ✅ **Modelo Pessoa Atualizado**
**Arquivo:** `app/Models/Pessoa.php` (backup: `Pessoa_original_perfis.php`)

**Campos adicionados ao fillable:**
- `email` - Email da pessoa
- `perfil` - Perfil de acesso (user, admin, gestor, fiscal, construtor, visualizador)
- `password` - Senha hashada para login

**Casts adicionados:**
- `password` => 'hashed'

**Relacionamentos adicionados:**
- `user()` - Relacionamento com modelo User

### ✅ **Views Atualizadas**

#### **Criação de Pessoa**
**Arquivo:** `resources/views/pessoa/create.blade.php`

Campos adicionados:
- Email (input)
- Perfil (select com opções de perfil)
- Senha (input password)

#### **Edição de Pessoa**
**Arquivo:** `resources/views/pessoa/edit.blade.php`

Campos adicionados:
- Email (input)
- Perfil (select com opções de perfil)
- Senha (input password)

### ✅ **Controller Atualizado**
**Arquivo:** `app/Http/Controllers/PessoaController.php`

**Método store():**
```php
$request->validate([
    'nome' => 'required|string|max:255',
    'cpf' => ['required', 'string', new CpfValido],
    'lotacao_id' => 'required|exists:lotacoes,id',
    'status' => 'required|in:ativo,inativo,pendente',
    'email' => 'nullable|email|max:255',
    'perfil' => 'nullable|in:user,admin,gestor,fiscal,construtor,visualizador',
    'password' => 'nullable|string|min:8',
]);
```

**Método update():**
```php
$request->validate([
    'nome' => 'required|string|max:255',
    'cpf' => ['required', 'string', new CpfValido],
    'lotacao_id' => 'required|exists:lotacoes,id',
    'status' => 'required|in:ativo,inativo,pendente',
    'email' => 'nullable|email|max:255',
    'perfil' => 'nullable|in:user,admin,gestor,fiscal,construtor,visualizador',
    'password' => 'nullable|string|min:8',
]);

// Se houver senha, fazer hash
if ($request->filled('password')) {
    $requestData['password'] = Hash::make($request->password);
} else {
    // Remover senha do array se não foi preenchida
    unset($requestData['password']);
}
```

### ✅ **Migration Criada**
**Arquivo:** `database/migrations/2025_10_27_000001_add_profile_and_email_to_pessoas_table.php`

```php
public function up(): void
{
    Schema::table('pessoas', function (Blueprint $table) {
        $table->string('email')->nullable()->after('nome');
        $table->string('perfil')->default('user')->after('email');
        $table->string('password')->nullable()->after('perfil');
    });
}
```

## ✅ **EXECUTADO COM SUCESSO**

### 1. Migration Executada
✅ Colunas adicionadas à tabela `pessoas`:
- `email` VARCHAR(255)
- `perfil` VARCHAR(50) DEFAULT 'user'
- `password` VARCHAR(255)

### 2. Dados Populados
✅ Todas as 10 pessoas existentes foram atualizadas com:
- Email no formato: primeironomeultimonome@teste.com
- Perfil: user
- Senha: 12345678 (hashada)

**Exemplos:**
- João Silva Santos → joaosantos@teste.com
- Maria Oliveira Costa → mariacosta@teste.com
- Pedro Henrique Almeida → pedroalmeida@teste.com

## 📋 **Campos Adicionados à Tabela `pessoas`**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `email` | VARCHAR(255) | SIM | NULL | Email para login |
| `perfil` | VARCHAR(50) | NÃO | 'user' | Perfil de acesso |
| `password` | VARCHAR(255) | SIM | NULL | Senha hashada |

## 🔐 **Perfis Disponíveis**

1. **user** - Usuário Básico
2. **visualizador** - Visualizador/Consultor
3. **construtor** - Construtor/Fornecedor
4. **fiscal** - Fiscal de Obra
5. **gestor** - Gestor de Contratos
6. **admin** - Administrador
7. **master** - Super Administrador (não disponível para seleção)

## 📝 **Formato de Email Sugerido**

Para pessoas existentes sem email:
- Primeiro nome + último nome + @teste.com
- Exemplo: João Silva → joaosilva@teste.com
- Senha padrão: `12345678`

## ✅ **Arquivos Modificados**

- ✅ `app/Models/Pessoa.php` (backup criado)
- ✅ `resources/views/pessoa/create.blade.php`
- ✅ `resources/views/pessoa/edit.blade.php`
- ✅ `app/Http/Controllers/PessoaController.php`
- ✅ `database/migrations/2025_10_27_000001_add_profile_and_email_to_pessoas_table.php` (criado)
- ✅ `popular_pessoas_usuarios.php` (script criado)

