<?php
namespace App\Repositories\Interfaces;

Interface ActivityLogInterface{
    /**
     * @param $event
     * @param $wallet
     * @return void
     */
    public static function createWalletLog($event, $wallet): void;

    /**
     * @return void
     */
    public static function deleteWalletLog(): void;

    /**
     * @param $event
     * @param $wallet
     * @param $openingBalance
     * @return void
     */
    public static function saveMoneyLog($event, $wallet, $openingBalance): void;

    /**
     * @param $wallet
     * @param $beforeStatus
     * @return void
     */
    public static function saveWalletStatusLog($wallet, $beforeStatus): void;
}
