<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawLimit extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @return WithdrawLimit
     */
    public function build(): WithdrawLimit
    {
        return $this
            ->subject('Wallet Deactivated')
            ->markdown('mails.wallet-deactivation');
    }
}
