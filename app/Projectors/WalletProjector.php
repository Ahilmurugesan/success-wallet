<?php

namespace App\Projectors;

use App\Events\WalletCreated;
use App\Events\WalletUpdated;
use App\Events\WalletDeleted;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Models\ActivityLog;
use App\Models\Wallet;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class WalletProjector extends Projector
{
    /**
     * @param  WalletCreated  $event
     * @return void
     */
    public function onWalletCreated(WalletCreated $event): void
    {
        $wallet = Wallet::create([
                    'uuid' => $event->aggregateRootUuid(),
                    'user_id' => $event->userId,
                    'status' => $event->status,
                ]);

        ActivityLogRepository::createWalletLog($event, $wallet);
    }

    /**
     * @param  MoneyAdded  $event
     * @return void
     */
    public function onMoneyAdded(MoneyAdded $event): void
    {
        $wallet = Wallet::uuid($event->aggregateRootUuid());

        $openingBalance = $wallet->balance;

        $wallet->balance += $event->amount;
        $wallet->save();

        ActivityLogRepository::saveMoneyLog($event, $wallet, $openingBalance);
    }

    /**
     * @param  MoneySubtracted  $event
     * @return void
     */
    public function onMoneySubtracted(MoneySubtracted $event): void
    {
        $wallet = Wallet::uuid($event->aggregateRootUuid());

        $openingBalance = $wallet->balance;

        $wallet->balance -= $event->amount;
        $wallet->save();

        ActivityLogRepository::saveMoneyLog($event, $wallet, $openingBalance);
    }

    /**
     * @param  WalletDeleted  $event
     * @return void
     */
    public function onWalletDeleted(WalletDeleted $event): void
    {
        Wallet::uuid($event->aggregateRootUuid())->delete();

        ActivityLogRepository::deleteWalletLog();
    }

    /**
     * @param  WalletUpdated  $event
     * @return void
     */
    public function onWalletUpdated(WalletUpdated $event): void
    {
        $wallet = Wallet::uuid($event->aggregateRootUuid());

        $beforeStatus = $wallet->status;

        $wallet->status = $event->status;
        $wallet->save();

        ActivityLogRepository::saveWalletStatusLog($wallet, $beforeStatus);
    }
}
