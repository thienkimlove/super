<?php

namespace App\Console\Commands;

use App\NetworkClick;
use Illuminate\Console\Command;

class RemoveInactiveLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:inactive';

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
        $lines = file(storage_path('logs/log_persec.log'), FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
           if (strpos($line, 'persec.mobi:80') !== false && strpos($line, '&status=-1') !== false) {
               $tempStr = explode('network_id=3&', $line);
               $tempStr = explode('&amount', $tempStr[1]);
               $tempStr = explode('&subid=', $tempStr[0]);

               $subId = $tempStr[1];
               $offerId = str_replace('offer_id=', '', $tempStr[0]);
               NetworkClick::where('network_offer_id', $offerId)
                   ->where('network_id', 3)
                   ->where('sub_id', $subId)
                   ->delete();
           }
        }
    }
}
