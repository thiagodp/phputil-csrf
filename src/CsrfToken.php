<?php
namespace phputil\csrf;

class CsrfToken {

    public string $value; // TODO: Make it readonly when migrating to PHP 8

    public function __construct( string $value ) {
        $this->value = $value;
    }

    public function hasAllValidCharacters() : bool {
        return preg_match( '/^[a-zA-Z0-9]+$/', $this->value );
    }

    public function __toString(): string {
        return $this->value;
    }
}
