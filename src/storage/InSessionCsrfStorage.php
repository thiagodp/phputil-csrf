<?php
namespace phputil\csrf;

const DEFAULT_SESSION_KEY = 'csrf';

class InSessionCsrfStorage implements CsrfStorage {

    private string $sessionKey;

    public function __construct(
        string $sessionKey = DEFAULT_SESSION_KEY
    ) {
        $this->sessionKey = $sessionKey;
    }

    /** @inheritDoc */
    public function loadToken(): ?CsrfToken {

        $this->checkIfSessionIsActive( 'load' );

        $value = $_SESSION[ $this->sessionKey ] ?? null;

        return $value === null ? null : new CsrfToken( $value );
    }

    /** @inheritDoc */
    public function saveToken( CsrfToken $token ): void {

        $this->checkIfSessionIsActive( 'save' );

        $_SESSION[ $this->sessionKey ] = $token->value;
    }


    /** @inheritDoc */
    public function removeToken(): bool {

        $this->checkIfSessionIsActive( 'remove' );

        if ( ! isset( $_SESSION[ $this->sessionKey ] ) ) {
            return false;
        }

        unset( $_SESSION[ $this->sessionKey ] );
        return true;
    }

    private function checkIfSessionIsActive( string $operation ): void {
        if ( session_status() !== PHP_SESSION_ACTIVE ) {
            throw new CsrfException( "Session must be active to $operation the CSRF token. Please use session_start() before using the middleware." );
        }
    }

}
?>