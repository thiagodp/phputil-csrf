<?php
namespace phputil\csrf;

interface CsrfStorage {

    /**
     * Loads the current token. It returns `null` if no token is stored.
     *
     * @throws CsrfException
     */
    public function loadToken(): ?CsrfToken;

    /**
     * Saves the given token.
     *
     * @param CsrfToken $token Token to save.
     * @throws CsrfException
     */
    public function saveToken( CsrfToken $token ): void;

    /**
     * Removes a saved token. Returns `true` if the token was removed or `false` if the token does not exist.
     *
     * @throws CsrfException
     */
    public function removeToken(): bool;
}
