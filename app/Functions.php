<?php


namespace App;


use Carbon\Carbon;
use Exception;

class Functions
{
    public static $api_url = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&format=json';

    protected static function get($api_url)
    {

        $result = [];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);

            if ($curl_errno == 0) {
                $result = json_decode($output,true);
            }

        } catch (Exception $e) {

        }

        return $result;
    }

    public static function apiGrab($options = [])
    {
        if (!isset($options['sd'])) {
            $options['sd'] = 01;
            $options['sm'] = 01;
            $options['sy'] = 2016;
        }

        if (!isset($options['ed'])) {
            $today =  Carbon::now();
            $options['ed'] = $today->day;
            $options['em'] = $today->month;
            $options['ey'] = $today->year;
        }

        $query = http_build_query($options);

       return self::get(self::$api_url. '&'. $query);

    }

    public static function offers($options)
    {

    }

}