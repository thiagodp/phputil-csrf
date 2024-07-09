<?php
use phputil\csrf\HeaderBasedCsrfStrategy;
use phputil\csrf\CsrfOptions;
use phputil\csrf\InMemoryCsrfStorage;
use phputil\router\FakeHttpRequest;
use phputil\router\FakeHttpResponse;
use phputil\router\Router;
use function phputil\csrf\csrf;

describe( 'HeaderBasedCsrfStrategy', function() {

    it( 'produces a CSRF-Token header with the CSRF token when no token is stored', function() {

        $strategy = new HeaderBasedCsrfStrategy();
        $storage = new InMemoryCsrfStorage(); // No token stored

        $app = new Router();
        $app->use( csrf( [], $strategy, $storage ) );
        $app->get( '/', function( $req, $res ) {} );

        $req = new FakeHttpRequest();
        $req->withMethod( 'GET' )->withURL( '/' );
        $res = new FakeHttpResponse();

        $app->listen( [ 'req' => $req, 'res' => $res ] );

        expect( $res->isStatus( 400 ) )->toBeFalsy();
        expect( $res->hasHeader( $strategy->getResponseHeaderName() ) )->toBeTruthy();
    } );


    it( 'produces a CSRF-Token header with the CSRF token when tokens match', function() {

        $options = new CsrfOptions();
        $options->tokenLength = 3;
        $options->disableTokenMasking = true;

        $token = 'foo';

        $strategy = new HeaderBasedCsrfStrategy();
        $storage = new InMemoryCsrfStorage( $token ); // Stored token

        $app = new Router();
        $app->use( csrf( $options, $strategy, $storage ) );
        $app->get( '/', function( $req, $res ) {} );

        $req = new FakeHttpRequest();
        $req->withHeader( $strategy->getRequestHeaderName(), $token ); // CSRF token as Header
        $req->withMethod( 'GET' )->withURL( '/' );

        $res = new FakeHttpResponse();

        $app->listen( [ 'req' => $req, 'res' => $res ] );

        expect( $res->isStatus( 400 ) )->toBeFalsy();
        expect( $res->hasHeader( $strategy->getResponseHeaderName() ) )->toBeTruthy();
    } );

} );
?>