<?php
namespace phputil\csrf;

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

interface CsrfStrategy {

    public function setOptions( CsrfOptions $options ): CsrfStrategy;

    public function getOptions(): CsrfOptions;

    public function setStorage( CsrfStorage $storage ): CsrfStrategy;

    public function getStorage(): ?CsrfStorage;

    /**
     * Executes the strategy.
     *
     * @param \phputil\router\HttpRequest $req Request
     * @param \phputil\router\HttpResponse $res Response
     * @param bool $stop If `true` it does not proceed to the functions after the middleware. By default it is `false`.
     * @return void
     *
     * @throws \LogicException
     */
    public function execute( HttpRequest $req, HttpResponse $res, bool &$stop = false ): void;
}

?>