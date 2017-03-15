<?php

namespace App\Console\Commands;

use App\Network;
use App\NetworkClick;
use App\Offer;
use Illuminate\Console\Command;

class OfferCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function feed($network)
    {
        $feed_url = $network->cron;
        // $feed_url = 'http://onetulip.afftrack.com/apiv2/?key=e661cf4c3909b1490ec1ac489349f66c&action=offer_feed';

        $offers = json_decode(file_get_contents($feed_url), true);

        $listCurrentNetworkOfferIds = [];

        if (isset($offers['offers'])) {
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

                Offer::updateOrCreate(['net_offer_id' => $offer['id']], [
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

                $listCurrentNetworkOfferIds[] = $offer['id'];
            }
        }

        #update cac offer tu dong khong co trong API ve status inactive.

        if ($listCurrentNetworkOfferIds) {
            $listCurrentNetworkOfferIds = array_unique($listCurrentNetworkOfferIds);

            Offer::where('auto', true)
                ->where('network_id', $network->id)
                ->whereNotIn('net_offer_id', $listCurrentNetworkOfferIds)
                ->update(['status' => false]);

        }

        $this->line('Total Offers : '.count($offers));
    }

    private function cpway($network)
    {
        //$url = 'http://intrexmedia.com/api.php?key=758be7ff505de4ad';
        $feed_url = $network->cron;
        $offers = json_decode(file_get_contents($feed_url), true);

        $listCurrentNetworkOfferIds = [];

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

            Offer::updateOrCreate(['net_offer_id' => $offer['offer_id']], [
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

            $listCurrentNetworkOfferIds[] = $offer['offer_id'];

        }

        #update cac offer tu dong khong co trong API ve status inactive.

        if ($listCurrentNetworkOfferIds) {
            $listCurrentNetworkOfferIds = array_unique($listCurrentNetworkOfferIds);

            Offer::where('auto', true)
                ->where('network_id', $network->id)
                ->whereNotIn('net_offer_id', $listCurrentNetworkOfferIds)
                ->update(['status' => false]);

        }

        $this->line('Total Offers : '.count($offers));
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $networks = Network::all();
        foreach ($networks as $network) {
            if ($network->cron) {
                if ($network->type == 'onetulip') {
                    $this->feed($network);
                    $this->line('Network :' . $network->name . 'is type onetulip have cron='.$network->cron);
                } else if ($network->type == 'cpway') {
                    $this->cpway($network);
                    $this->line('Network :' . $network->name . 'is type cpway have cron='.$network->cron);
                }
            } else {
                $this->line('Network :' . $network->name . ' has no cron');
            }
        }
    }
}
