<?php
require_once __DIR__ . '/../src/generation.php';

describe( '#generateToken', function() {

    it( 'generates an empty string when receives zero', function() {
        $token = generateToken( 0 );
        expect( $token )->toBe( '' );
    } );

    it( 'generates an empty string when receives a negative value', function() {
        $token = generateToken( -1 );
        expect( $token )->toBe( '' );
    } );

    it( 'generates with an odd size', function() {
        $size = 7;
        $token = generateToken( $size );
        expect( strlen( $token ) )->toBe( $size );
    } );

    it( 'generates with an even size', function() {
        $size = 10;
        $token = generateToken( $size );
        expect( strlen( $token ) )->toBe( $size );
    } );

    it( 'has only hexadecimal characters', function() {
        $token = generateToken( 500 );
        expect( preg_match( '/^[a-h0-9]+$/', $token ) )->toBeTruthy();
    } );

} );
?>