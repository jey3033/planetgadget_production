<?php

namespace Kredivo\Payment\Library;

class Api
{
    public static function get_redirection_url($params)
    {
        $params['server_key'] = Config::$server_key;

        $result = Request::post(
            Config::get_api_endpoint() . '/checkout_url',
            $params
        );

        return $result->redirect_url;
    }

    public static function response_notification($data = array())
    {
        // header('Content-Type: application/json');

        $default = array(
            "status"  => "OK",
            "message" => "Notification has been received",
        );
        $data = array_merge($default, $data);

        return json_encode($data);
    }

    public static function confirm_order_status($params)
    {
        $result = Request::get(
            Config::get_api_endpoint() . '/update?' . http_build_query($params)
        );

        return $result;
    }
}
