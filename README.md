# phputil-csrf

> 🔌 CSRF middleware for [phputil/router](https://github.com/thiagodp/router)


## Installation

> Requires phputil/router **v0.2.14+**

```bash
composer require phputil/csrf
```

## Usage

### With default options

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

## Documentation

```PHP
/**
 * Returns a CSRF middleware.
 *
 * @param array|CsrfOptions $options CSRF options.
 * @param CsrfStrategy $strategy Strategy. By default it uses a cookie-based strategy with default options.
 * @param CsrfStorage $storage Storage. By default it uses a session-based storage with default options.
 *
 * @return callable
 */
function csrf( $options = [], CsrfStrategy $strategy = null, CsrfStorage $storage = null ): callable;
```

Options are:
- `disableTokenMasking` (bool, default `false`) indicates if token masking should be disabled. By default, the randomly-generated token is masked through a XOR operation with a random key and then converted to base64.
- `disableTokenRenewal` (bool, default `false`) indicates if token renewal should be disabled.
- `tokenLength` (int, default `20`) indicates the desired token length. Note that this is the UNMASKED token length.

Available Strategies:

- `CookieBasedCsrfStrategy`: uses cookies to send and receive the CSRF token. That's the default strategy.
- `HeaderBasedCsrfStrategy`: uses HTTP headers to send and receive the CSRF token.

**Note**: You can create your own CSRF strategy by implementing the interface `CsrfStrategy`.

Available Storages:

- `InSessionCsrfStorage`: uses PHP's `$_SESSION` to store the CSRF token in order to compare it later.

**Note**: You can create your own CSRF storage by implementing the interface `CsrfStorage`.


## License

[MIT](LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)