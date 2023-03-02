<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneySubtracted extends ShouldBeStored
{
    /**
     * @param  int  $amount
     * @param  array  $transferDetails
     */
    public function __construct(
        public int $amount,
        public array  $transferDetails
    ) {}
}
