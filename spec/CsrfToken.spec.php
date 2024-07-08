<?php
use phputil\csrf\CsrfToken;

describe( 'CsrfToken', function() {

    $this->tokens = [
        '',
        '$a@',
        '0123456789?az', // "?" is invalid
        '0123456789?AZ', // "?" is invalid
        '-' . generateToken( 10 ),
        generateToken( 10 ) . '-',
        generateToken( 5 ) . ' ' . generateToken( 5 )
    ];

    foreach( $this->tokens as $token ) {
        it( "can verify invalid token '$token'", function() use ( $token ) {
            expect( ( new CsrfToken( $token ) )->hasAllValidCharacters() )->toBeFalsy();
        } );
    }

    it( 'can verify valid token values', function() {
        $tokens = [
            'a1',
            '0123456789abcdef',
            '0123456789ABCDEF'
        ];
        foreach( $tokens as $token ) {
            expect( ( new CsrfToken( $token ) )->hasAllValidCharacters() )->toBeTruthy();
        }
    } );

} );
?>