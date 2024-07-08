<?php
//
// Masking/unmasking functions inspired by https://github.com/slimphp/Slim-Csrf/blob/1.x/src/Guard.php
//

function maskToken( string $token ) : string {
    // Key length must be the same as the token's length
    $key = random_bytes( strlen( $token ) );
    // Key XOR with the token
    return base64_encode( $key . ( $key ^ $token ) );
}

function unmaskToken(string $maskedToken): string {

    $decoded = base64_decode( $maskedToken, true );
    if ( $decoded === false ) {
        return '';
    }

    $tokenLength = strlen( $decoded ) / 2;

    $key = substr( $decoded, 0, $tokenLength );
    $realMaskedToken = substr( $decoded, $tokenLength, $tokenLength );

    // Key XOR with the token
    return $key ^ $realMaskedToken;
}

?>