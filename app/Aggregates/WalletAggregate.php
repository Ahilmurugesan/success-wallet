<?php

namespace App\Aggregates;

use App\Events\MoreMoneyNeeded;
use App\Events\WalletCreated;
use App\Events\WalletUpdated;
use App\Events\WalletDeleted;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Events\WalletLimitHit;
use App\Exceptions\CouldNotCreateWallet;
use App\Exceptions\CouldNotUpdateMoney;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class WalletAggregate extends AggregateRoot
{
    protected int $balance = 0;
    protected string $status = '';
    protected int $walletLimit = 0;
    protected int $walletLimitHitInARow = 0;

    /**
     * @param  object  $user
     * @param  int  $status
     * @return WalletAggregate
     */
    public function createWallet(object $user, int $status): WalletAggregate
    {
        $this->recordThat(new WalletCreated($user->id, $status));

        return $this;
    }

    /**
     * @param  WalletCreated  $event
     * @return void
     */
    public function applyWalletCreated(WalletCreated $event): void
    {
        $this->status = $event->status;
    }

    /**
     * @param  int  $amount
     * @param  array  $transferDetails
     * @return $this
     */
    public function addMoney(int $amount, array $transferDetails=[]): WalletAggregate
    {
        if(!$this->isWalletActive())
        {
            throw CouldNotUpdateMoney::walletIsNotActive();
        }
        $this->recordThat(new MoneyAdded($amount, $transferDetails));

        return $this;
    }

    /**
     * @param  MoneyAdded  $event
     * @return void
     */
    public function applyMoneyAdded(MoneyAdded $event): void
    {
        $this->walletLimitHitInARow = 0;

        $this->balance += $event->amount;
    }

    /**
     * @param  int  $amount
     * @param  array  $transferDetails
     * @return WalletAggregate
     */
    public function subtractMoney(int $amount, array $transferDetails=[]): WalletAggregate
    {
        if(!$this->isWalletActive())
        {
            throw CouldNotUpdateMoney::walletIsNotActive();
        }

        if (!$this->hasSufficientFundsToSubtractAmount($amount)) {
            $this->recordThat(new WalletLimitHit());

            if ($this->needsMoreMoney()) {
                $this->recordThat(new MoreMoneyNeeded());
            }

            $this->persist();

            throw CouldNotUpdateMoney::notEnoughFunds($amount);
        }
         $this->recordThat(new MoneySubtracted($amount, $transferDetails));

        return $this;
    }

    /**
     * @param  MoneySubtracted  $event
     * @return void
     */
    public function applyMoneySubtracted(MoneySubtracted $event): void
    {
        $this->balance -= $event->amount;

        $this->walletLimitHitInARow = 0;
    }

    /**
     * @return $this
     */
    public function deleteWallet(): static
    {
        $this->recordThat(new WalletDeleted());

        return $this;
    }

    /**
     * @param  WalletDeleted  $event
     * @return void
     */
    public function applyWalletDeleted(WalletDeleted $event): void
    {
        $this->status = '';
    }

    /**
     * @return $this
     */
    public function walletUpdate(int $status): static
    {
        $this->recordThat(new WalletUpdated($status));

        return $this;
    }

    /**
     * @param  WalletUpdated  $event
     * @return void
     */
    public function applyWalletUpdated(WalletUpdated $event): void
    {
        $this->status = $event->status;
    }

    /**
     * @param  WalletLimitHit  $walletLimitHit
     * @return void
     */
    public function applyWalletLimitHit(WalletLimitHit $walletLimitHit): void
    {
        $this->walletLimitHitInARow++;
    }

    /**
     * @param  int  $amount
     * @return bool
     */
    private function hasSufficientFundsToSubtractAmount(int $amount): bool
    {
        return $this->balance - $amount >= $this->walletLimit;
    }

    /**
     * @return bool
     */
    private function isWalletActive(): bool
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    private function needsMoreMoney(): bool
    {
        return $this->walletLimitHitInARow >= 3;
    }
}
