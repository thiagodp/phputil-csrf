<?php
require_once 'vendor/autoload.php';

use phputil\csrf\BaseCsrfStrategy;
use phputil\csrf\CsrfStorage;
use phputil\csrf\CsrfToken;
use phputil\router\FakeHttpRequest;
use phputil\router\FakeHttpResponse;
use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

const BAD_REQUEST = 400;


class FakeCsrfStorage implements CsrfStorage {

    private string $value;

    public function __construct( string $value = '' ) {
        $this->value = $value;
    }

    public function loadToken(): ?CsrfToken {
        return new CsrfToken( $this->value );
    }

    public function saveToken( CsrfToken $token ): void {
        $this->value = $token->value;
    }

    public function removeToken(): bool {
        return true;
    }

}

describe( 'BaseCsrfStrategy', function() {

    it( 'throws an exception when the storage is not set', function() {

        $obj = new class extends BaseCsrfStrategy {
            public function getToken( HttpRequest $req): ?string {
                return null;
            }
            public function setToken( string $token, HttpResponse $req): void {}
        };

        expect( function() use ( $obj ) {
            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $obj->execute( $req, $res, $stop );
        } )->toThrow();
    } );

    describe( 'sends a bad request when', function() {

        it( 'receives no token', function() {

            $obj = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req): ?string {
                    return null;
                }
                public function setToken( string $token, HttpResponse $req): void {}
            };
            $obj->setStorage( new FakeCsrfStorage() );

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $obj->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();
        } );


        it( 'receives a token with an invalid size', function() {

            $obj = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req): ?string {
                    return '123';
                }
                public function setToken( string $token, HttpResponse $req): void {}
            };
            $obj->setStorage( new FakeCsrfStorage() );

            $obj->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $obj->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( $body )->toContain( 'size' ); // message
        } );


        it( 'receives a token with an invalid format', function() {

            $obj = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req): ?string {
                    $token = '#' . str_repeat( '0', $this->getOptions()->tokenLength - 1 );
                    return $token;
                }
                public function setToken( string $token, HttpResponse $req): void {}
            };
            $obj->setStorage( new FakeCsrfStorage() );

            $obj->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $obj->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( $body )->toContain( 'format' ); // message
        } );


        it( 'receives a token that differs from the stored one', function() {

            $obj = new class extends BaseCsrfStrategy {
                public function getToken( HttpRequest $req): ?string {
                    return 'bar';
                }
                public function setToken( string $token, HttpResponse $req): void {}
            };
            $obj->setStorage( new FakeCsrfStorage( 'foo' ) );

            $obj->getOptions()->tokenLength = 3;
            $obj->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

            $req = new FakeHttpRequest();
            $res = new FakeHttpResponse();
            $stop = false;

            $obj->execute( $req, $res, $stop );

            expect( $stop )->toBeTruthy();
            expect( $res->isStatus( BAD_REQUEST ) )->toBeTruthy();

            $body = implode( ' ', $res->dumpObject()->body );
            expect( strtolower( $body ) )->toContain( 'invalid' ); // message
        } );

    } );


    it( 'does not return a bad request when tokens match', function() {

        $obj = new class extends BaseCsrfStrategy {
            public function getToken( HttpRequest $req): ?string {
                return 'foo';
            }
            public function setToken( string $token, HttpResponse $req): void {}
        };
        $obj->setStorage( new FakeCsrfStorage( 'foo' ) );

        $obj->getOptions()->tokenLength = 3;
        $obj->getOptions()->disableTokenMasking = true; // Important to avoid changing the size

        $req = new FakeHttpRequest();
        $res = new FakeHttpResponse();
        $stop = false;

        $obj->execute( $req, $res, $stop );

        expect( $stop )->toBeFalsy();
        expect( $res->isStatus( BAD_REQUEST ) )->toBeFalsy();
    } );


} );
?>