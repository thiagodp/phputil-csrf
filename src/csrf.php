<?php
namespace phputil\csrf;

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

/**
 * Returns a CSRF middleware.
 *
 * @param array|CsrfOptions $options CSRF options.
 * @param CsrfStrategy $strategy Strategy. By default it uses a cookie-based strategy with default options.
 * @param CsrfStorage $storage Storage. By default it uses a session-based storage with default options.
 *
 * @return callable
 */
function csrf( $options = [], CsrfStrategy $strategy = null, CsrfStorage $storage = null ): callable {

    if ( $strategy === null ) {
        $strategy = new CookieBasedCsrfStrategy();
    }

    if ( $storage === null ) {
        $storage = new InSessionCsrfStorage();
    }

    $csrfOptions = ( $options instanceof CsrfOptions )
        ? $options
        : ( is_array( $options ) ? ( new CsrfOptions() )->fromArray( $options ) : new CsrfOptions() );

    $strategy->setOptions( $csrfOptions );
    $strategy->setStorage( $storage );

    return function( HttpRequest $req, HttpResponse $res, bool &$stop = false ) use ( $strategy ) {
        $strategy->execute( $req, $res, $stop );
    };
}
