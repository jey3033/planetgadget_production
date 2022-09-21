<?php

namespace Kredivo\Payment\Library;

class Config
{
    public static $server_key;
    public static $api_version   = 'v2';
    public static $is_production = false;

    const SANDBOX_ENDPOINT    = 'https://sandbox.kredivo.com/kredivo';
    const PRODUCTION_ENDPOINT = 'https://api.kredivo.com/kredivo';

    public static function get_api_endpoint()
    {
        $sandbox    = self::SANDBOX_ENDPOINT . '/' . self::$api_version;
        $production = self::PRODUCTION_ENDPOINT . '/' . self::$api_version;
        return self::$is_production ? $production : $sandbox;
    }
}
