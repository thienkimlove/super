<?php

namespace App\Console\Commands;

use App\Network;
use App\NetworkClick;
use App\Offer;
use App\Site;
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
                Site::feed($network);
                $this->line('Network :' . $network->name . ' have cron='.$network->cron);
            } else {
                $this->line('Network :' . $network->name . ' has no cron');
            }
        }
    }
}
