<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class WalletCreated extends ShouldBeStored
{
    /**
     * @param  int  $userId
     * @param  int  $status
     */
    public function __construct(
        public int $userId,
        public int $status
    ) {}
}
