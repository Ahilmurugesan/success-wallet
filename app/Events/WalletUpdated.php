<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class WalletUpdated extends ShouldBeStored
{
    /**
     * @param  int  $status
     */
    public function __construct(
        public int $status
    ) {}
}
