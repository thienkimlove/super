<?php

namespace App;

use File;
use GuzzleHttp\Client;

class Site
{

    public static function parseOffer($offer, $network)
    {
        $isIphone = false;
        $isIpad = false;
        $android = false;
        $ios = false;
        $countries = [];
        $netOfferId = null;
        $redirectLink = null;
        $payout = 0;
        $offerName = null;
        $geoLocations = null;
        $devices = null;
        $realDevice = 1;


        #style 1

        if (isset($offer['devices'])) {
            $devices = $offer['devices'];
        }

        if (isset($offer['Platforms'])) {
            $devices = explode(',', $offer['Platforms']);
        }

        foreach ($devices as $device) {

            $deviceType = null;

            if (is_array($device)) {
                $deviceType = strtolower($device['device_type']);
            } else {
                $deviceType = strtolower($device);
            }

            if (strpos($deviceType, 'iphone') !== false) {
                $isIphone = true;
            }
            if (strpos($deviceType, 'ipad') !== false) {
                $isIpad = true;
            }
            if (strpos($deviceType, 'droid') !== false) {
                $android = true;
            }

            if ($isIphone && $isIpad) {
                $ios = true;
            }
        }

        if ($ios && $android) {
            $realDevice = 2;
        } else if ($android) {
            $realDevice = 4;
        } else if ($ios) {
            $realDevice = 5;
        } else if ($isIphone) {
            $realDevice = 6;
        } else if ($isIpad) {
            $realDevice = 7;
        }

        if (isset($offer['countries'])) {
            foreach ($offer['countries'] as $country) {
                $countries[]  = $country['code'];
            }
        }

        if (isset($offer['id'])) {
            $netOfferId = $offer['id'];
        }

        if (isset($offer['offer_id'])) {
            $netOfferId = $offer['offer_id'];
        }

        if (isset($offer['ID'])) {
            $netOfferId = $offer['ID'];
        }


        if (isset($offer['tracking_link'])) {
            $redirectLink = $offer['tracking_link'].'&s1=#subId';
        }

        if (isset($offer['tracking_url'])) {
            $redirectLink = str_replace('&s1=&s2=&s3=', '&s1=#subId', $offer['tracking_url']);
        }

        if (isset($offer['Tracking_url'])) {
            $redirectLink = $offer['Tracking_url'].'&s1=#subId';
        }


        if (isset($offer['payout'])) {
            $payout = round(floatval($offer['payout'])/intval(env('RATE_CRON')), 2);
        }

        if (isset($offer['rate'])) {
            $payout = round(floatval(str_replace('$', '', $offer['rate']))/intval(env('RATE_CRON')), 2);
        }

        if (isset($offer['Payout'])) {
            $payout = round(floatval(str_replace('$', '', $offer['Payout']))/intval(env('RATE_CRON')), 2);
        }

        if (isset($offer['name'])) {
            $offerName = str_limit( $offer['name'], 250);
        }

        if (isset($offer['offer_name'])) {
            $offerName = str_limit( $offer['offer_name'], 250);
        }

        if (isset($offer['Name'])) {
            $offerName = str_limit( $offer['Name'], 250);
        }

        if (isset($offer['geos'])) {
            $geoLocations = implode(',', $offer['geos']);
        }

        if ($countries) {
            $geoLocations = implode(',', $countries);
        }

        if (isset($offer['Countries'])) {
            $geoLocations = $offer['Countries'];
        }

        $checkExisted =  Offer::where('net_offer_id', $netOfferId)->where('network_id', $network->id)->count();

        if ($checkExisted == 0) {
            Offer::create([
                'net_offer_id' => $netOfferId,
                'name' => $offerName,
                'redirect_link' => $redirectLink,
                'click_rate' => $payout,
                'allow_devices' => $realDevice,
                'geo_locations' => $geoLocations,
                'network_id' => $network->id,
                'status' => true,
                'auto' => true
            ]);
        }

        return $netOfferId;
    }


    public static function feed($network)
    {
        $feed_url = $network->cron;
        // $feed_url = 'http://onetulip.afftrack.com/apiv2/?key=e661cf4c3909b1490ec1ac489349f66c&action=offer_feed';
        $offers = self::getUrlContent($feed_url);
        $listCurrentNetworkOfferIds = [];

        if ($offers) {
            $rawContent = isset($offers['offers']) ? $offers['offers'] : $rawContent;
            foreach ($rawContent as $offer) {
                $listCurrentNetworkOfferIds[] = self::parseOffer($offer, $network);
            }
        }


        #update cac offer tu dong khong co trong API ve status inactive.

        if ($listCurrentNetworkOfferIds && !env('NO_UPDATE_CRON')) {
            $listCurrentNetworkOfferIds = array_unique($listCurrentNetworkOfferIds);

            Offer::where('auto', true)
                ->where('network_id', $network->id)
                ->whereNotIn('net_offer_id', $listCurrentNetworkOfferIds)
                ->update(['status' => false]);

        }
        return $total;
    }

    public static function download($file_source, $file_target) {
        $rh = fopen($file_source, 'rb');
        $wh = fopen($file_target, 'w+b');
        if (!$rh || !$wh) {
            return false;
        }

        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 4096)) === FALSE) {
                return false;
            }
            flush();
        }

        fclose($rh);
        fclose($wh);

        return true;
    }

    public static function getUrlContent($url)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        $response = [];

        try {
            $client = new Client();
            $res = $client->request('GET', $url);
            $ticketResponse = $res->getBody();
            $response = json_decode($ticketResponse, true);
        } catch (\Exception $e) {

        }

        return $response;
    }
}