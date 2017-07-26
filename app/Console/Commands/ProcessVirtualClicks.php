<?php

namespace App\Console\Commands;

use App\Offer;
use App\VirtualLog;
use Illuminate\Console\Command;
use DB;

class ProcessVirtualClicks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'virtual:clicks';

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

    private function virtualCurl($isoCode, $url, $userAgent)
    {
        $username = 'lum-customer-theway_holdings-zone-nam-country-'.strtolower($isoCode);
        $password = '99oah6sz26i5';
        $port = 22225;
        $session = mt_rand();
        $super_proxy = 'zproxy.luminati.io';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username-session-$session:$password");
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Connection: Keep-Alive',
            'Keep-Alive: 10'
        ));
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($curl);
        curl_close($curl);

        $additionUrl = null;

        try {
            $xml = simplexml_load_file($result);
            $additionUrl = $xml->xpath("//meta[@http-equiv='refresh']");
            \Log::info('follow_this_url='.$additionUrl);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        if ($additionUrl) {
            return $this->virtualCurl($isoCode, $additionUrl, $userAgent);
        } else {
            return $result;
        }

    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userAgentFiles = resource_path('useragent10.txt');
        $lines = file($userAgentFiles, FILE_IGNORE_NEW_LINES);
        try {
            $virtualClicks = VirtualLog::where('sent', false)
                ->orderBy('created_at', 'asc')
                ->limit(1000)
                ->get();
            if ($virtualClicks->count() > 0) {
                foreach ($virtualClicks as $virtualClick) {
                    $offer = Offer::find($virtualClick->offer_id);
                    $userAgent = $lines[array_rand($lines)];
                    $redirectLink = null;
                    if ($offer->id == 3) {
                        $redirectLink = 'https://www.whatismybrowser.com/detect/what-is-my-user-agent';
                    } else if ($offer->id == 4) {
                        $redirectLink = 'http://whatismyipaddress.com/';
                    } else {
                        $redirectLink = $offer->redirect_link;
                    }

                    $response = $this->virtualCurl($virtualClick->user_country, $redirectLink, $userAgent);
                    $virtualClick->update([
                        'user_agent' => $userAgent,
                        'response' => $response,
                        'sent' => true
                    ]);
                }
            }

        } catch (\Exception $e) {
           $this->line($e->getMessage());
        }
    }
}