<?php
namespace phputil\csrf;

use phputil\csrf\CsrfToken;

class InMemoryCsrfStorage implements CsrfStorage {

    private ?string $value = null;

    public function __construct( ?string $value = null ) {
        $this->value = $value;
    }

    public function loadToken(): ?CsrfToken {
        return $this->value === null ? null : new CsrfToken( $this->value );
    }

    public function saveToken( CsrfToken $token ): void {
        $this->value = $token->getValue();
    }

    public function removeToken(): bool {
        $this->value = null;
        return true;
    }

}

?>