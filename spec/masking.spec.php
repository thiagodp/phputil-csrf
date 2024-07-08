<?php
require_once __DIR__ . '/../src/masking.php';

describe( 'masking', function() {

    it( 'operations are reversible', function() {
        $token = 'the quick brown fox jumped over the lazy frog';
        $masked = maskToken( $token );
        $unmasked = unmaskToken( $masked );
        expect( $unmasked )->toBe( $token );
    } );

} );
?>