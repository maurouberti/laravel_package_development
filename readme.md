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

Criado um repositório até este passo.

```
git init
git add .
git commit -m "first commit"
git remote add origin https://github.com/maurouberti/laravel_package_development.git
git push -u origin master
```

Passo 5: Criando pacote para tradução
---

Criar estrutura de pastas e arquivos dentro do diretório **modules**

```
modules
├── Location
│   ├── Http
│   │   └── Middleware
│   │        └── LocaleMiddleware.php
│   ├── Providers
│   │        └── LocationServiceProvider.php
│   ├── Routes
│   │   └── web.php
```

Criar as *classes* dos arquivos:

```
web.php (rotas)
LocaleMiddleware.php (middleware)
```

Mover o arquivo de **app/Http/Middleware/LocaleMiddleware.php** para **modules/Location/Http/Middleware/LocaleMiddleware.php**

Alterar o namespace do arquivo **modules/Location/Http/Middleware/LocaleMiddleware.php**

´´´
namespace Modules\Location\Http\Middleware;
´´´

Alterar o registro da *middleware* no *app/Http/Kernel.php*

```
\Modules\Location\Http\Middleware\Locale::class,
```

Registrar *Rotas* no arquivo **modules/Location/Providers/LocationServiceProvider** (não será utilizado controller nas rotas do *location*)

```
public function boot() {
    Route::middleware(['web'])->group(__DIR__.'/../Routes/web.php');
}
```

> Não retirar a **middleware(['web'])** do arquivo **modules/Pages/Providers/PageServiceProvider.php**, porque após *redirecior*, é chamado novamente a view da Page.

Criar a rota que altera a tradução no arquivo **modules/Location/Routes/web.php** e retirar do arquivo **modules/Pages/Routes/web.php**

```
Route::get('/locale/{locale}', function($locale) {
    request()->session()->put('locale', $locale);
    return redirect('/pages');
});
```

Registrar no arquivo **config/app.php**

```
/*
 * Package Service Providers...
 */
Modules\Location\Providers\LocationServiceProvider::class,
```

Criado um repositório até este passo.

```
git add .
git commit -m "Criando pacote para tradução"
git remote add origin https://github.com/maurouberti/laravel_package_development.git
git push -u origin master
```

Passo6: Publicando arquivos e merge de configurações

Se criar uma pasta **resources/views/vendor** e  dela outra com o nome do alias que esta registrado no **loadViewsFrom** do arquivo **modules/Paages/Providers/PageServiceProvides**, os arquivos que criar nela subistituirá os arquivo da modulo

```
resources
├── views
│   ├── vendor
│   │   └── Page
│   │        └── index.blade.php
```

> O mesmo se aplica para *loadTranslationsFrom*, 

Para deixar disponível a criação dos arquivo automaticamente abra o arquivo **modules/Paages/Providers/PageServiceProvides** e *publique*

```
$this->publishes([
    __DIR__.'/../Views', resource_path('views/pages/Page'),
], 'views');
$this->publishes([
    __DIR__.'/../Lang', resource_path('lang/pages/Page'),
], 'lang');
```

> resource_path() retorna o caminho da pasta resource

Criar os arquivo **modules/Pages/config/pages.php** e **modules/Pages/public/assents/style.css**

```
modules
├── Pages
│   ├── config
│   │   └── pages.php
│   ├── public
│   │   └── assents
│   │        └── style.css
```

e publicar

```
$this->publishes([
    __DIR__.'/../config/pages.php', config_path('pages.php'),
], 'config');
$this->publishes([
    __DIR__.'/../public', public_path('vendor/pages'),
], 'public');
```

Para publicar (criar as pastas) execute o comando

```
php artisan vendor:publish --tag=views
```
ou
```
php artisan vendor:publish --tag=lang
```
ou
```
php artisan vendor:publish --tag=conf
```
ou
```
php artisan vendor:publish --tag=public
```

ou de todos *publish* do *provider*
```
php artisan vendor:publish --provider="Modules\Pages\Providers\PageServiceProvider"
```

Para forçar criar novamente
```
php artisan vendor:publish --provider="Modules\Pages\Providers\PageServiceProvider" --force
```


