<?php
namespace phputil\csrf;

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

const KEY__REQUEST_HEADER_NAME = 'requestHeaderName';
const KEY__RESPONSE_HEADER_NAME = 'responseHeaderName';

const DEFAULT_REQUEST_HEADER_NAME = 'X-CSRF-Token';
const DEFAULT_RESPONSE_HEADER_NAME = 'CSRF-Token';

class HeaderBasedCsrfStrategy extends BaseCsrfStrategy {

    protected array $strategyOptions;

    /**
     * @param array $strategyOptions Strategy options, which is an array that can contain:
     *  - `"requestHeaderName"` => with the name of the expected request header. By default it is `"X-CSRF-Token"`.
     *  - `"responseHeaderName"` => with the name of the produced response header. By default it is `"CSRF-Token"`.
     *
     */
    public function __construct(
        array $strategyOptions = []
    ) {
        $this->strategyOptions = $strategyOptions;
    }

    public function getRequestHeaderName(): string {
        return $this->strategyOptions[ KEY__REQUEST_HEADER_NAME ] ?? DEFAULT_REQUEST_HEADER_NAME;
    }

    public function getResponseHeaderName(): string {
        return $this->strategyOptions[ KEY__RESPONSE_HEADER_NAME ] ?? DEFAULT_RESPONSE_HEADER_NAME;
    }

    //
    // BaseCsrfStrategy
    //

    /** @inheritDoc */
    public function getToken( HttpRequest $req ): ?string {
        $headerName = $this->getRequestHeaderName();
        return $req->header( $headerName );
    }

    /** @inheritDoc */
    public function setToken( string $token, HttpResponse $res ): void {
        $headerName = $this->getResponseHeaderName();
        $res->header( $headerName, $token );
    }
}
