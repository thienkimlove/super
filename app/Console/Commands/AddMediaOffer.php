<?php

namespace App\Console\Commands;

use App\Offer;
use Illuminate\Console\Command;

class AddMediaOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add media offers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'http://intrexmedia.com/api.php?key=758be7ff505de4ad';
        $offers = json_decode(file_get_contents($url), true);
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
                'click_rate' => round(floatval(str_replace('$', '', $offer['rate']))/2, 2),
                'allow_devices' => $devices,
                'geo_locations' => implode(',', $offer['geos']),
                'network_id' => 1,
                'status' => true,
                'auto' => true
            ]);

        }

    }
}
