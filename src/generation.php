<?php

/**
 * Generates a token of the given length.
 *
 * @param int $length Token length
 * @return string
 */
function generateToken( int $length ): string {
    if ( $length <= 0 ) {
        return '';
    }
    $token = bin2hex( random_bytes( intval( $length / 2 ) + 1 ) );
    return substr( $token, 0, $length );
}

?>