<?php
require_once 'vendor/autoload.php';

use phputil\csrf\BaseCsrfStrategy;
use phputil\csrf\InMemoryCsrfStorage;
use phputil\router\FakeHttpRequest;
use phputil\router\FakeHttpResponse;
use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

const BAD_REQUEST = 400;

describe( 'BaseCsrfStrategy', function() {

    it( 'throws an exception when the storage is not set', function() {

        $strategy = new class extends BaseCsrfStrategy {
            public function getToken( HttpRequest $req ): ?string {
                return null;
            }
            public function setToken( string $token, HttpResponse $req ): void {}
        };

        expect( function() use ( $strategy ) {
            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $strategy->execute( $req, $res, $stop );
        } )->toThrow();
    } );

    it( 'generates a new token when there is no stored token and no token is sent', function() {

        $strategy = new class extends BaseCsrfStrategy {
            public ?string $token = null; // No token sent
            public function getToken( HttpRequest $req ): ?string {
                return $this->token;
            }
            public function setToken( string $token, HttpResponse $req ): void {
                $this->token = $token;
            }
        };

        $strategy->setStorage( new InMemoryCsrfStorage() ); // No token stored

        $req = new FakeHttpRequest();
        $res = new FakeHttpResponse();
        $stop = false;

        $strategy->execute( $req, $res, $stop );

        expect( $stop )->toBeFalsy();
        expect( $res->isStatus( BAD_REQUEST ) )->toBeFalsy();

        expect( $strategy->getToken( $req ) )->not->toBeNull();
    } );

    describe( 'sends a bad request when', function() {

        it( 'receives no token BUT there is a stored token', function() {

            $strategy = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req ): ?string {
                    return null; // No token sent
                }
                public function setToken( string $token, HttpResponse $req ): void {}
            };

            $strategy->setStorage( new InMemoryCsrfStorage( 'foo' ) ); // Stored token

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $strategy->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();
        } );


        it( 'receives a token with an invalid size', function() {

            $strategy = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req ): ?string {
                    return '123';
                }
                public function setToken( string $token, HttpResponse $req ): void {}
            };

            $strategy->setStorage( new InMemoryCsrfStorage( '123456' ) );
            $strategy->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $strategy->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( $body )->toContain( 'size' ); // message
        } );


        it( 'receives a token with an invalid format', function() {

            $strategy = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req ): ?string {
                    $token = '#' . str_repeat( '0', $this->getOptions()->tokenLength - 1 );
                    return $token;
                }
                public function setToken( string $token, HttpResponse $req ): void {}
            };

            $token = str_repeat( '0', $strategy->getOptions()->tokenLength );
            $strategy->setStorage( new InMemoryCsrfStorage( $token ) );
            $strategy->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $strategy->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( $body )->toContain( 'format' ); // message
        } );


        it( 'receives a token that differs from the stored one', function() {

            $strategy = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req): ?string {
                    return 'bar';
                }
                public function setToken( string $token, HttpResponse $req): void {}
            };

            $strategy->setStorage( new InMemoryCsrfStorage( 'foo' ) );
            $strategy->getOptions()->tokenLength = 3;
            $strategy->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $strategy->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( strtolower( $body ) )->toContain( 'invalid' ); // message
        } );

    } );


    it( 'does not return a bad request when tokens match', function() {

        $strategy = new class extends BaseCsrfStrategy {
            public function getToken( HttpRequest $req): ?string {
                return 'foo';
            }
            public function setToken( string $token, HttpResponse $req): void {}
        };

        $strategy->setStorage( new InMemoryCsrfStorage( 'foo' ) );
        $strategy->getOptions()->tokenLength = 3;
        $strategy->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

        $req = new FakeHttpRequest();
        $res = new FakeHttpResponse();
        $stop = false;

        $strategy->execute( $req, $res, $stop );

        expect( $stop )->toBeFalsy();
        expect( $res->isStatus( BAD_REQUEST ) )->toBeFalsy();
    } );


} );
?>