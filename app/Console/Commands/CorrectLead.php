<?php

namespace App\Console\Commands;

use App\NetworkClick;
use Illuminate\Console\Command;

class CorrectLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correct:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run once time to fill all new fields in network_clicks table';

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
        $oldLeads = NetworkClick::join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->join('offers', 'clicks.offer_id', '=', 'offers.id')
            ->selectRaw('clicks.id as click_id, offers.id as offer_id, network_clicks.sub_id as lead_sub_id, network_clicks.amount as lead_amount')
            ->whereNull('network_clicks.offer_id')
            ->limit(10)
            ->get();

        foreach ($oldLeads as $oldLead) {
            dd($oldLead);
        }


    }
}
