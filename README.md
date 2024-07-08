# phputil-csrf

> 🔌 CSRF middleware for [phputil/router](https://github.com/thiagodp/router)


## Installation

> Requires phputil/router **v0.2.14+**

```bash
composer require phputil/csrf
```

## Usage

```php
require_once 'vendor/autoload.php';
use phputil\router\Router;
use function phputil\crsf\crsf; // Step 1: Declare the namespace usage for the function.
$app = new Router();

$app->use( crsf() ); // Step 2: Invoke the function to use it as a middleware.

$app->get( '/', function( $req, $res ) {
    $res->send( 'Hello' );
} );
$app->listen();
```


## License

[MIT](LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)