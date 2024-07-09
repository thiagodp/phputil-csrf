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

Argument `$options` (array, default `[]`) can have the following keys:
- `disableTokenMasking` (bool, default `false`) indicates if token masking should be disabled.
    - Note: By default, the randomly-generated token is masked through a XOR operation with a random key and then converted to base64.
- `disableTokenRenewal` (bool, default `false`) indicates if token renewal should be disabled.
- `tokenLength` (int, default `20`) indicates the desired token length. Note that this is the **unmasked** token length.

### Available Strategies

The following classes are available:

- `CookieBasedCsrfStrategy`: uses cookies to send and receive the CSRF token. That's the default strategy.
    - Its constructor receives two arguments, both optional:
        - `$strategyOptions` (array, default `[]`) that can have:
            - `"cookieName"`: the name of the CSRF cookie. By default, it is `csrf_token`.
        - `$cookieOptions` (array, default `[]`) that can have the same options as PHP's [setcookie()](https://www.php.net/manual/en/function.setcookie).
- `HeaderBasedCsrfStrategy`: uses HTTP headers to send and receive the CSRF token.
    - Its constructor receives one argument, `$strategyOptions` (array, default `[]`), that is optional and can have:
        - `"requestHeaderName"`: expected request header. By default it is `"X-CSRF-Token"`.
        - `"responseHeaderName"`: produced response header. By default it is `"CSRF-Token"`.

**Note**: You can create your own CSRF strategy by implementing the interface `CsrfStrategy`.

### Available Storages

The following classes are available:

- `InSessionCsrfStorage`: uses PHP's `$_SESSION` to store the CSRF token in order to compare it later.
    - Its constructor receives one optional argument, `$sessionKey` (string), which is the key stored in the `$_SESSION` variable. By default it is `csrf`.

**Note**: You can create your own CSRF storage by implementing the interface `CsrfStorage`.


## License

[MIT](LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)