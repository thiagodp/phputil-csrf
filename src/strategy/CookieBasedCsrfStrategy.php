<?php
namespace phputil\csrf;

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

const KEY__COOKIE_NAME = 'cookieName';

const DEFAULT_COOKIE_NAME = 'csrf_token';

class CookieBasedCsrfStrategy extends BaseCsrfStrategy {

    protected array $strategyOptions = [];
    protected array $cookieOptions;

    /**
     * @param array $strategyOptions Strategy options, which is an array that can contain:
     *  - `"cookieName"` => with the token cookie name. By default it is `"csrf_token"`.
     *
     * @param array $cookieOptions Cookie options. @see https://www.php.net/manual/en/function.setcookie
     */
    public function __construct(
        array $strategyOptions = [],
        array $cookieOptions = []
    ) {
        $this->strategyOptions = $strategyOptions;
        $this->cookieOptions = $cookieOptions;
    }

    protected function getCookieName(): string {
        return $this->strategyOptions[ KEY__COOKIE_NAME ] ?? DEFAULT_COOKIE_NAME;
    }

    //
    // BaseCsrfStrategy
    //

    /** @inheritDoc */
    public function getToken( HttpRequest $req ): ?string {
        $tokenCookieName = $this->getCookieName();
        return $req->cookie( $tokenCookieName );
    }

    /** @inheritDoc */
    public function setToken( string $token, HttpResponse $res ): void {
        $tokenCookieName = $this->getCookieName();
        $res->cookie( $tokenCookieName, $token, $this->cookieOptions );
    }
}