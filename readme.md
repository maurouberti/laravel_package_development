Passo 1: Criando esqueleto do pacote
---

Criar diretório **modules**

Mapear o diretório **modules** no arquivo **composer.json**

```
"autoload": {
    "classmap": [
        "database/seeds",
        "database/factories"
    ],
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
},
```

Recarregar o autoloader:

```
composer dump
```

Criar estrutura de pastas e arquivos dentro do diretório **modules**

```bash
modules
├── Pages
│   ├── Http
│   │   └── Controllers
│   │        └── PagesController.php
│   ├── Providers
│   ├── Routes
│   │   └── web.php
│   ├── Views
│   │   └── index.blade.php
│   └── Page.php
```

Criar as *classes* dos arquivos:

```
web.php (rotas)
Page.php (model)
PagesController.php (controller)
index.blade.php (view)
```

Passo: 2 Criando service provider
---

Criar a *classe* do arquivo **PageServiceProvider.php**

```
modules
├── Pages
│   ├── Providers
│   │   └── PageServiceProvider
```

Registrar *Rotas* e *Views* no arquivo **PageServiceProvider**

```
public function boot() {
    Route::namespace('Modules\Pages\Http\Controllers')->group(__DIR__.'/../Routes/web.php');
    $this->loadViewsFrom(__DIR__.'/../Views', 'Page');
}
```

Registrar *Controlles* e *Models* no arquivo **config/app.php**

```
/*
 * Package Service Providers...
 */
Modules\Pages\Providers\PageServiceProvider::class,
```

Passo 3: Incluindo migrations no pacote
---

Criar diretório **Migrations**

```
modules
├── Pages
│   ├── Migrations
```

Roda comando *artisan* para criar a *migration*

```
php artisan make:migration create_pages_table --path=modules/Pages/Migrations
```

Registrar *migrates* no arquivo **PageServiceProvider**

```
public function boot() {
    ...
    $this->loadMigrationsFrom(__DIR__.'/../Migrations');
}
```

Configurar o arquivo **.env**

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_package_development
DB_USERNAME=root
DB_PASSWORD=123456
```

Criar o banco de dados

```
echo "create database laravel_package_development" | mysql -u root -p
```

Rodar a *migration*

```
php artisan migrate
```

Passo 4: Incluindo traduções no pacote
---

Criar arquivos de tratução e diretórios **Lang**

```
modules
├── Pages
│   ├── Lang
│   │   ├── en
│   │   │   └── pages.php
│   │   ├── pt-br
│   │   │   └── pages.php
```

Adicione no arquivo **modules/Pages/Providers/PageServiceProvider**

```
$this->loadTranslationsFrom(__DIR__.'/../Lang', 'Page');
```

Coloque uma interpolação no arquivo da view (**modules/Page/Views/index.blade.php**) com a função trans (translate)

```
<h1>{{ trans('Page::pages.title') }}</h1>
```

No arquivo de rota **modules/Pages/Routes/web.php** criar uma rota para guardar em uma *session* o *locale* selecionado

```
Route::get('/locale/{locale}', function ($locale) {
    request()->session()->put('locale', $locale);
    return redirect('/pages');
});
```

Criar um *middleware*

```
php artisan make:middleware LocaleMiddleware
```

No arquivo **app/Http/Middleware/LocaleMiddleware.php** pegar a *session locale* e alterar dinamicamente o *locale* do **config/app.php**

```
public function handle($request, Closure $next)
{
    if ($request->session()->has('locale')) {
        $locale = $request->session()->get('locale');
        \App::setLocale($locale);
    }
    return $next($request);
}
```

Registrar no **app/Http/Kernel.php** a *middleware*

```
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\LocaleMiddleware::class,
    ],
];
```

No arquivo **modules/Pages/Providers/PageServiceProvider** adicione a **middleware* antes de chamar as rotas

```
Route::namespace('Modules\Pages\Http\Controllers')->middleware(['web'])->group(__DIR__.'/../Routes/web.php');
```
