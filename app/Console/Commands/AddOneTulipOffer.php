<?php

namespace App\Console\Commands;

use App\Network;
use App\Offer;
use Illuminate\Console\Command;

class AddOneTulipOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add auto offer for network one tulip';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function feed($network = null)
    {
        $feed_url = $network->cron;
       // $feed_url = 'http://onetulip.afftrack.com/apiv2/?key=e661cf4c3909b1490ec1ac489349f66c&action=offer_feed';

        $offers = json_decode(file_get_contents($feed_url), true);

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
                    'click_rate' => round(floatval($offer['payout'])/2, 2),
                    'allow_devices' => $devices,
                    'geo_locations' => implode(',', $countries),
                    'network_id' => $network->id,
                    'status' => true,
                    'auto' => true
                ]);
            }
        }
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
            if ($network->cron && $network->type == 'cpway') {
               $this->feed($network);
            }
        }
    }
}
