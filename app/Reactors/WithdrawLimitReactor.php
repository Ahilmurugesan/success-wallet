<?php

namespace App\Reactors;

use App\Events\MoreMoneyNeeded;
use App\Mail\WithdrawLimit;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class WithdrawLimitReactor extends Reactor implements ShouldQueue
{
    /**
     * @param  MoreMoneyNeeded  $event
     * @return void
     */
    public function __invoke(MoreMoneyNeeded $event): void
    {
        $account = Wallet::where('uuid', $event->aggregateRootUuid())->first();

        Mail::to($account->user)->send(new WithdrawLimit());
    }
}
