<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Interfaces\ActivityLogInterface;
use Illuminate\Support\Facades\Auth;

class ActivityLogRepository implements ActivityLogInterface
{
    /**
     * @param $event
     * @param $wallet
     * @return void
     */
    public static function createWalletLog($event, $wallet): void
    {
        $logs = [
            'user_id' => $event->userId,
            'module' => 'Wallet',
            'action' => 'Wallet Created',
            'log_data' => collect(array_merge($wallet->toArray(), [
                'opening_balance'   => 0,
                'closing_balance'   => 0
            ]))
        ];
        ActivityLog::saveLog($logs);
    }

    /**
     * @return void
     */
    public static function deleteWalletLog(): void
    {
        $logs = [
            'user_id' => Auth::id(),
            'module' => 'Wallet',
            'action' => 'Wallet Deleted',
            'log_data' => null
        ];
        ActivityLog::saveLog($logs);
    }

    /**
     * @param $event
     * @param $wallet
     * @param $openingBalance
     * @return void
     */
    public static function saveMoneyLog($event, $wallet, $openingBalance): void
    {
        $transferDetails = $event->transferDetails;
        $is_transfer = (bool) count($transferDetails);
        $logs = [
            'user_id' => $wallet->user_id,
            'module' => 'Wallet',
            'action' => 'Money Added',
            'log_data' => collect(array_merge($wallet->toArray(), [
                'opening_balance'   => $openingBalance,
                'closing_balance'   => $wallet->balance,
                'is_transfer'       => $is_transfer,
                'transfer_amount'   => $is_transfer ? $transferDetails['amount'] : '',
                'transferred_from'  => $is_transfer ? $transferDetails['transferred_from'] : '',
                'transferred_to'   => $is_transfer ? $transferDetails['transferred_to'] : ''
            ]))
        ];

        ActivityLog::saveLog($logs);
    }

    /**
     * @param $wallet
     * @param $beforeStatus
     * @return void
     */
    public static function saveWalletStatusLog($wallet, $beforeStatus): void
    {
        $logs = [
            'user_id' => $wallet->user_id,
            'module' => 'Wallet',
            'action' => 'Wallet Status Updated',
            'log_data' => collect(array_merge($wallet->toArray(), [
                'before_status'   => $beforeStatus,
                'current_balance'   => $wallet->status
            ]))
        ];
        ActivityLog::saveLog($logs);
    }
}
