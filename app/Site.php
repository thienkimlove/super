<?php

namespace App;

use File;
use GuzzleHttp\Client;

class Site
{
    public static function feed($network)
    {
        $feed_url = $network->cron;
        // $feed_url = 'http://onetulip.afftrack.com/apiv2/?key=e661cf4c3909b1490ec1ac489349f66c&action=offer_feed';
        $offers = self::getUrlContent($feed_url);

        $listCurrentNetworkOfferIds = [];
        $total = 0;

        if ($offers) {
            if (isset($offers['offers'])) {
                $total = count($offers['offers']);
                if ($total > 0) {
                    foreach ($offers['offers'] as $offer) {

                        $devices = 1;
                        $isIphone = false;
                        $isIpad = false;
                        $android = false;
                        $ios = false;
                        if ($offer['devices']) {
                            foreach ($offer['devices'] as $device) {
                                if (strpos(strtolower($device['device_type']), 'iphone') !== false) {
                                    $isIphone = true;
                                }
                                if (strpos(strtolower($device['device_type']), 'ipad') !== false) {
                                    $isIpad = true;
                                }
                                if (strpos(strtolower($device['device_type']), 'droid') !== false) {
                                    $android = true;
                                }

                                if ($isIphone && $isIpad) {
                                    $ios = true;
                                }
                            }
                        }


                        if ($ios && $android) {
                            $devices = 2;
                        } else if ($android) {
                            $devices = 4;
                        } else if ($ios) {
                            $devices = 5;
                        } else if ($isIphone) {
                            $devices = 6;
                        } else if ($isIpad) {
                            $devices = 7;
                        }

                        $countries = [];

                        foreach ($offer['countries'] as $country) {
                            $countries[]  = $country['code'];
                        }

                        $checkExisted =  Offer::where('net_offer_id', $offer['id'])->where('network_id', $network->id)->count();

                        if ($checkExisted == 0) {
                            Offer::create([
                                'net_offer_id' => $offer['id'],
                                'name' => str_limit( $offer['name'], 250),
                                'redirect_link' => $offer['tracking_link'].'&s1=#subId',
                                'click_rate' => round(floatval($offer['payout'])/intval(env('RATE_CRON')), 2),
                                'allow_devices' => $devices,
                                'geo_locations' => implode(',', $countries),
                                'network_id' => $network->id,
                                'status' => true,
                                'auto' => true
                            ]);
                        }

                        $listCurrentNetworkOfferIds[] = $offer['id'];
                    }
                }

            } else {
                $total = count($offers);
                if ($total > 0) {
                    foreach ($offers as $offer) {

                        $devices = null;
                        $isIphone = false;
                        $isIpad = false;
                        $android = false;
                        $ios = false;
                        if ($offer['devices']) {
                            foreach ($offer['devices'] as $device) {
                                if (strpos(strtolower($device), 'iphone') !== false) {
                                    $isIphone = true;
                                }
                                if (strpos(strtolower($device), 'ipad') !== false) {
                                    $isIpad = true;
                                }
                                if (strpos(strtolower($device), 'droid') !== false) {
                                    $android = true;
                                }

                                if ($isIphone && $isIpad) {
                                    $ios = true;
                                }
                            }
                        }


                        if ($ios && $android) {
                            $devices = 2;
                        } else if ($android) {
                            $devices = 4;
                        } else if ($ios) {
                            $devices = 5;
                        } else if ($isIphone) {
                            $devices = 6;
                        } else if ($isIpad) {
                            $devices = 7;
                        }

                        $checkExisted = Offer::where('net_offer_id', $offer['offer_id'])->where('network_id', $network->id)->count();

                        if ($checkExisted == 0) {
                            Offer::create([
                                'net_offer_id' => $offer['offer_id'],
                                'name' => str_limit( $offer['offer_name'], 250),
                                'redirect_link' => str_replace('&s1=&s2=&s3=', '&s1=#subId', $offer['tracking_url']),
                                'click_rate' => round(floatval(str_replace('$', '', $offer['rate']))/intval(env('RATE_CRON')), 2),
                                'allow_devices' => $devices,
                                'geo_locations' => implode(',', $offer['geos']),
                                'network_id' => $network->id,
                                'status' => true,
                                'auto' => true
                            ]);
                        }

                        $listCurrentNetworkOfferIds[] = $offer['offer_id'];
                    }
                }
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
        ini_set('memory_limit', '-1');
        $wrapperUrl = 'http://103.21.150.81/wapper.php?url='.urlencode($url);
        return json_decode(file_get_contents($wrapperUrl), true);
    }
}