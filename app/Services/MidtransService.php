<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * @param array $params
     * @return string snap_token
     * @throws \Exception
     */
    public function createTransaction(array $params): string
    {
        return Snap::getSnapToken($params);
    }
}
