# PHP Modern App Skeleton
This project is a simple proof of concepts, the idea is to generate a simple structure with the basic tools
necessary to build PHP applications without a framework.

## Dependencies

Here is a list of the libraries used in this project.

 * [PSR-7 HTTP Message](https://github.com/laminas/laminas-diactoros)
 * [PSR-7 Router - Dispatcher](https://route.thephpleague.com/)
 * [PSR-11 Dependency Injection Container](https://container.thephpleague.com/)
 * [PSR-15 Middleware Dispatcher](https://github.com/oscarotero/middleland)
 * [PSR-15 Session Middleware](https://github.com/psr7-sessions/storageless)
 * [Template Engine - Plates](https://platesphp.com/)
 * [Database - FluentPDO](https://github.com/envms/fluentpdo)
 * [Validator - Sirius Validation](https://github.com/siriusphp/validation)
 * [Environment - PHP DotEnv](https://github.com/vlucas/phpdotenv)
 * [Database Migrations - Phinx](https://github.com/cakephp/phinx)
 * [Error Handler - Whoops](https://github.com/filp/whoops)

Refer to the official documentation to understand how it works.

```
TODO:
    - Authentication
```

### Installation
```shell script
git clone https://github.com/ursuleacv/php-mvc-skeleton.git
```

Project dependencies are managed by [Composer](https://getcomposer.org/) which can be easily installed with a couple of commands:
```shell script
curl -sS https://getcomposer.org/installer | php
```
```shell script
sudo mv ~ /composer.phar/usr/local/bin/composer
```
With `composer` installed and being in the root of the project, you only need to run the following command:
```shell script
composer install
```

### HTTP Server
The easiest way to test the operation of the application is with the PHP built-in server (only in development):
```shell script
php -S localhost:8000 -t public
```

### PSR-4 Autoloading
Project classes are loaded automatically thanks to Composer, as long as the PSR-4 standard of [PHP-FIG](https://www.php-fig.org/psr/psr-4/) is followed.
```php
namespace App\SomeDirectory\ClassName;
```

### Environment
The configuration parameters (environment variables) are applied from the **.env ** file (rename the .env.example) that is located in the root of the project and follow the format: `SECRET_KEY="12345"`, to having access to these variables takes care of any of these methods:
```php
$key = $_ENV['SECRET_KEY'];
$key = $_SERVER['SECRET_KEY'];
```

### Routes
The project routes are defined in the file `app/routes.php`, they respond to any Http verb (get | post | put | patch | delete), each route is associated with a __handler__, this can be a callback or a class:
```php
$router->get ('/example', function (ServerRequestInterface $request): ResponseInterface {
    return new HtmlResponse('Hello World!');
});

$router->get ('/users/{id: number}', [App\Controllers\UsersController::class, 'show']);
```
The __handler__ needs to return a [Response](#psr-7-http-response) object.

### Controllers
Controllers are created in the `app/Controllers` folder. Build dependencies are resolved automatically by the [Container](#psr-11-container) installed in the project:
```php
public function __construct (View $view, User $user)
{
    $this->view = $view;
    $this->user = $user;
}
```

### PSR-11 Container
The project has an IoC container that resolves the dependencies of a class automatically **ReflectionContainer**:
```php
$container
   ->add(Acme\Foo::class)
   ->setShared();
```

### Service Provider
The **ReflectionContainer** is not capable of resolving a dependency if it has scalar arguments or depends on an interface | contract, for these cases it is necessary to teach the Container how to resolve the dependency, a service provider takes care of this. Service providers inherit from `AbstractServiceProvider` and are stored in the` app/Provider` folder, it is also necessary to register them in the `App \ Application` class with the` getProviders () `method.
```php
protected $provides = ['Some\ClassInterface'];
public function register()
{
    $this->getContainer()->add('Some\ClassInterface', 'Some\Class');
}
```

### PSR-7 HTTP Request
The Request object is a wrapper of the global variables $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER. It is used (among many other things) to obtain get | post data through an object-oriented interface, this object travels through the entire application:
```php
public function index(ServerRequestInterface $request): ResponseInterface
{
    $query = $request->getQueryParams();
    $params = $request->getParsedBody();
    return view('home', compact('query', 'params'));
}
```
It can be obtained in several ways:
* The Router | Dispatcher injects a ServerRequest on all handlers.
* Request it in the constructor through the interface `Psr\Http\Message\ServerRequestInterface`.
* Through the global function (helpers.php) request().

### PSR-7 HTTP Response
This object is necessary to send some kind of response to the client (browser), there is only one instance of Response in the entire application cycle, and it can be obtained in several ways:

* Creating an instance of `Laminas\Diactoros\Response` or derivatives.
* Request it in the constructor through the interface `Psr\Http\Message\ServerRequestInterface`.
* Through the [Template Engine](#template-engine).
* Through the global function (helpers.php) response().

With the Response object it is only necessary to set the corresponding headers, and the content, the project is in charge of preparing and sending the response to the client.
```php
$response->withStatus(200);
$response->withHeader('Content-Type', 'application/json');
$response->getBody()->write(json_encode([
    "dns" => "1.1.1.1",
    "alt_dns" => "1.0.0.1"
]));
```
### PSR-15 Middleware Dispatcher
The project has two PSR-15 Middleware Dispatchers, the Router and the Kernel, the middleware is stored in the `app/Http/Middleware` folder and must implement the` Psr \ Http \ Server \ MiddlewareInterface` interface. If they are going to be used in the Router to manipulate the Request (before) or the Response (after) when dispatching a route, it is only necessary to register them in the `app/Http/routes.php` file:
```php
//General
$router->middleware(new Acme\AuthMiddleware);
//Specific
$router->get('/', [Controller::class, 'create'])->middleware(new Acme\AuthMiddleware);
```
If the middleware needs to be applied independently to the Router's handler, that is, it affects another layer of the application, it must be registered in the `App\Application` class with the` vendorMiddleware() `method.

### PSR-7 Sessions StorageLess
The handling of sessions is managed in a different way, in PHP the extension `ext/session` is generally used with the super-global` $_SESSION`, internally a file is created on the server, and an identifier that is stored in a cookie. A `StorageLess` session does not create this file, the information is stored in a cookie through a JWT token, this type of session has several [advantages](https://github.com/psr7-sessions/storageless#advantages) over the conventional sessions. To create the JWT token a randomly generated key is used, this key is obtained with the environment variable `APP_KEY`.
To generate the `APP_KEY` you can use:
```shell script
openssl rand -hex 32
```
The session is injected into the Request by means of a Middleware and can be obtained in the following way:
```php
/**
 * @var \PSR7Sessions\Storageless\Session\SessionInterface $session
 */
$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
$session->set('counter', $session->get('counter', 0) + 1);
```
If for some reason you need to replace the conventional `ext/session` sessions, it is recommended to use some of these Middleware:
 * https://github.com/jasny/session-middleware.
 * https://github.com/mezzio/mezzio-session

Additional configuration in the `src/Providers/SessionServiceProvider.php` file.

### Database
The project uses the FluentPDO, a PHP SQL query builder using PDO.
```php
$this->db->from('users')->where('id', 1)->fetch();
```
Additional configuration in the `asrc/ServiceProviders/DatabaseServiceProvider.php` file.

#### Database Migrations & Seeding
You can keep track of changes on the design of the database with migrations, you only need to configure the connection in the **phinx.php** file, to create the migration you have to execute the following command:
```shell script
vendor/bin/phinx create CreateUsersTable
```
This creates a class in the `/db/migrations` folder with a specific name, in this class the migration is defined. The following command is used to execute the migrations:
```shell script
vendor/bin/phinx migrate  
```
If there are any errors you can undo the changes from the last migration with:
```shell script
vendor/bin/phinx rollback  
```
In the same way, Seeders can be created and run:
```shell script
vendor/bin/phinx seed:create UserTableSeeder
vendor/bin/phinx seed:run -v
```

### Validation
The Validator object can be obtained by construction or by creating an instance in any method, all the documentation can be found on the author's website [SiriusValidation](http://www.sirius.ro/php/sirius/validation/).
[Custom](https://www.sirius.ro/php/sirius/validation/rule_factory.html) validation rules can be created to extend the functionality, these rules must be stored individually in the `app/ValidationRules` and must be registered in the Service Provider `src/ServiceProviders/ValidationServiceProvider.php`.
```php
 $validator->add([
    'email: Email' => 'required|email|unique(users, email)',
    'password: Password' => 'required|minlength(8)|maxlength(24)'
]);
if($validator->validate($data)) {
    $auth->login($data);
} else {
    $errors = $validator->getMessages();
}
```

### Template Engine
The View Engine can be obtained by construction in a Controller using the class `PhpMvcCore\View`, the` render() `method is in charge of looking for the template and processing it:
```php
public function profile(ServerRequestInterface $request): ResponseInterface
{
    $user = $this->findUser->byId(1);
    return $this->view->render('profile', compact('user'));
}
```
It can also be obtained through the `view` function
```php
return \view('profile', [
    'user' => $user,
]);
```
Then in `profle.php` view.
```html
<p> <?= >user->email;?> </p>
```
The templates are stored in the `resources/views` folder.

Additional configuration in the `src/Providers/TemplateServiceProvider.php` file.

### Debug
The error Handler of the project captures all the errors-exceptions that are not contemplated in a tryCatch block, indicates in a friendly way the error stack and even highlights the code with the error, obviously it is only recommended in the development environment, 
to enable it you have to set the environment variable: 
```sh
APP_DEBUG = true. 
```

### PSR-12 Validate
The project code can be validated and corrected with the PSR-12 standard of [PHP-FIG](https://www.php-fig.org/psr/psr-12/)
```shell script
vendor/bin/phpcs -n -p --colors --report=summary --standard=psr12 app/ --ignore=app/db*
```
or
```shell script
vendor/bin/phpcs -n -p --colors --standard=psr12 app/
```
To fix the errors:
```shell script
vendor/bin/phpcbf --standard=psr12 app/
```

### Customization
In the `src/ServiceProviders` folder the Service Providers of the project are stored, a large part of the configuration can be done directly in those classes, to extend the project, add functionalities, it is only necessary to create a new Service Provider without forgetting to register it in the `Container`.
