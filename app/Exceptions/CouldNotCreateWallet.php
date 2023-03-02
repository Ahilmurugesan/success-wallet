<?php

namespace App\Exceptions;

use DomainException;

class CouldNotCreateWallet extends DomainException
{
    /**
     * @return static
     */
    public static function walletAlreadyExists(): self
    {
        return new static("User already has a wallet");
    }
}
