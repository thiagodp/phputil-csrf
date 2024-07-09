<?php
namespace phputil\csrf;

require_once __DIR__ . '/../generation.php';
require_once __DIR__ . '/../masking.php';

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

abstract class BaseCsrfStrategy implements CsrfStrategy {

    protected CsrfOptions $options;
    protected ?CsrfStorage $storage = null;

    public function __construct() {
        $this->options = new CsrfOptions();
    }

    //
    // Abstract methods
    //

    abstract public function getToken( HttpRequest $req ): ?string;

    abstract public function setToken( string $token, HttpResponse $res ): void;

    //
    // CsrfStrategy
    //

    /** @inheritDoc */
    public function setOptions( CsrfOptions $options ): CsrfStrategy {
        $this->options = $options;
        return $this;
    }

    /** @inheritDoc */
    public function getOptions(): CsrfOptions {
        return $this->options;
    }

    /** @inheritDoc */
    public function setStorage( CsrfStorage $storage ): CsrfStrategy {
        $this->storage = $storage;
        return $this;
    }

    /** @inheritDoc */
    public function getStorage(): ?CsrfStorage {
        return $this->storage;
    }

    /** @inheritDoc */
    public function execute( HttpRequest $req, HttpResponse $res, bool &$stop = false ): void {

        if ( $this->storage === null ) {
            throw new \LogicException( 'Please set the storage in the strategy.' );
        }

        $options = $this->getOptions();
        $storedToken = $this->storage->loadToken();

        // No token is stored
        if ( $storedToken === null ) {

            // Generates a new token
            $token = generateToken( $options->tokenLength );

            // Stores the new token
            $this->getStorage()->saveToken( new CsrfToken( $token ) );

            // Mask the token before sending it
            if ( ! $options->disableTokenMasking ) {
                $token = maskToken( $token );
            }

            $this->setToken( $token, $res );
            return;
        }

        // Gets the token from the request
        $requestToken = $this->getToken( $req );

        // No token in the request
        if ( $requestToken === null || $requestToken === '' ) {
            $res->status( 400 )->send( 'Please send the CSRF token.' );
            $stop = true;
            return;
        }

        // Unmask
        if ( ! $options->disableTokenMasking ) {
            $requestToken = unmaskToken( $requestToken );
        }

        // Check size - NOTE: after unmasking (since masking changes the size) !
        if ( strlen( $requestToken ) != $options->tokenLength ) {
            $res->status( 400 )->send( 'Invalid CSRF token size.' );
            $stop = true;
            return;
        }

        // Check format
        $token = new CsrfToken( $requestToken );
        if ( ! $token->hasAllValidCharacters() ) {
            $res->status( 400 )->send( 'Invalid CSRF token format.' );
            $stop = true;
            return;
        }

        // Compare to the stored one
        if ( ! $storedToken->isEqualTo( $token ) ) {
            $res->status( 400 )->send( 'Invalid CSRF token.' );
            $stop = true;
            return;
        }

        // Renew the token
        if ( ! $options->disableTokenRenewal ) {

            $token = generateToken( $options->tokenLength );

            // Store the new token
            $this->getStorage()->saveToken( new CsrfToken( $token ) );

            // Mask the token before sending it
            if ( ! $options->disableTokenMasking ) {
                $token = maskToken( $token );
            }
        }

        // Sets the token in the response
        $this->setToken( $token, $res );
    }

}
