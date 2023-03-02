<?php

namespace App\Exceptions;

use DomainException;

class CouldNotUpdateMoney extends DomainException
{
    /**
     * @param  int  $amount
     * @return static
     */
    public static function notEnoughFunds(int $amount): self
    {
        return new static("Oops! Requested withdrawal amount {$amount} exceeds account balance!");
    }

    /**
     * @return CouldNotUpdateMoney
     */
    public static function walletIsNotActive(): self
    {
        return new static("Oops! Amount cannot be added (or) transferred because wallet is not active");
    }

    /**
     * @param  string  $type
     * @return CouldNotUpdateMoney
     */
    public static function walletNotFound(string $type = 'your'): self
    {
        return new static("Oops! No wallet associated for {$type} account");
    }
}
