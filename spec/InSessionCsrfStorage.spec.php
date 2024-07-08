<?php
use phputil\csrf\CsrfToken;
use phputil\csrf\InSessionCsrfStorage;

describe( 'InSessionCsrfStorage', function() {

    it( 'throws when trying to load a token without starting a session', function() {

        expect( function() {
            $storage = new InSessionCsrfStorage();
            $storage->loadToken();
        } )->toThrow();
    } );

    it( 'throws when trying to save a token without starting a session', function() {

        expect( function() {
            $storage = new InSessionCsrfStorage();
            $storage->saveToken( new CsrfToken( 'foo' ) );
        } )->toThrow();
    } );

    it( 'throws when trying to remove a token without starting a session', function() {

        expect( function() {
            $storage = new InSessionCsrfStorage();
            $storage->removeToken();
        } )->toThrow();
    } );

} );