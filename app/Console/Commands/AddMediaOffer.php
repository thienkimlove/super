<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MediaOffer;

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
            MediaOffer::updateOrCreate([
                'offer_id' => $offer['offer_id'],
            ],[
                'offer_id' => $offer['offer_id'],
                'offer_name' => $offer['offer_name'],
                'offer_preview_link' => $offer['preview_link'],
                'offer_tracking_link' => $offer['tracking_url'],
            ]);
        }
    }
}
