<?php
namespace phputil\csrf;

const DEFAULT_TOKEN_LENGTH = 20;

class CsrfOptions {

    public bool $disableTokenMasking = false;

    public bool $disableTokenRenewal = false;

    public int $tokenLength = DEFAULT_TOKEN_LENGTH;

    public function fromArray( array $options ): CsrfOptions {
        $this->disableTokenMasking = $options[ 'disableTokenMasking' ] ?? false;
        $this->disableTokenRenewal = $options[ 'disableTokenRenewal' ] ?? false;
        $this->tokenLength = $options[ 'tokenLength' ] ?? DEFAULT_TOKEN_LENGTH;
        return $this;
    }

}
?>