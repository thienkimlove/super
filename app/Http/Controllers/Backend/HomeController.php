<?php

namespace App\Http\Controllers\Backend;

use App\Click;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HomeController extends AdminController
{

    public function index()
    {
        $currentUser = auth('backend')->user();
        $content = [];
        $clicks = DB::table('clicks')->where('user_id', $currentUser->id);

        $query1 = clone $clicks;

        $records = $query1->groupBy('offer_id')
            ->select(DB::raw('count(*) as total'), 'offer_id')
            ->get();

        $totalMoney = 0;

        if ($records->count() > 0) {
            foreach ($records as $record) {
                $offer = Offer::find($record->offer_id);
                $totalMoney += $record->total * $offer->click_rate;
            }
        }

        $query2 = clone $clicks;

        $recordMonths = $query2->where('click_time', '>=', Carbon::now()->startOfMonth())
            ->groupBy('offer_id')
            ->select(DB::raw('count(*) as total'), 'offer_id')
            ->get();

        $totalMonth = 0;

        if ($recordMonths->count() > 0) {
            foreach ($recordMonths as $record) {
                $offer = Offer::find($record->offer_id);
                $totalMonth += $record->total * $offer->click_rate;
            }
        }

        $content['total_money'] = $totalMoney;
        $content['total_month'] = $totalMonth;

        $today =  Carbon::now()->toDateString();
        $offers =  Offer::whereHas('clicks', function($query) use ($today, $currentUser) {
            $query->whereBetween('updated_at', [$today.' 00:00:00', $today.' 23:59:59'])
                ->where('user_id', $currentUser->id);
        })->get();

        $recentOffers = Offer::whereHas('clicks', function($query) use ($currentUser) {
            $query->orderBy('updated_at', 'desc')
                ->where('user_id', $currentUser->id);
        })->limit(5)->get();

       return view('admin.index', compact('content', 'offers', 'recentOffers'));
    }

    public function control()
    {
        $content = [];

        $content['total_users'] = DB::table('users')->count();
        $content['active_users'] = DB::table('users')->where('status', true)->count();
        $content['total_clicks'] = DB::table('clicks')->count();
        $content['total_offers'] = DB::table('offers')->count();

        $today =  Carbon::now()->toDateString();
        /*$offers =  Offer::whereHas('clicks', function($query) use ($today) {
            $query->whereBetween('updated_at', [$today.' 00:00:00', $today.' 23:59:59']);
        })->get();*/

        $offers = Offer::latest('updated_at')->get();

        foreach ($offers as $offer) {
            $offer->net_click = null;
            $offer->net_lead = null;
            $offer->net_cr = null;
            $offer->site_click = $offer->clicks->count();

            if ($offer->network_id == 1) {
                //cpway.
                $stats = json_decode(file_get_contents('http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary'), true);

                if (isset($stats['stats_summary'])) {
                    foreach ($stats['stats_summary'] as $stat) {
                        if ($stat['id'] == $offer->net_offer_id) {
                            $offer->net_click = intval($stat['clicks']);
                            $offer->net_lead = intval($stat['leads']);
                            $offer->net_cr = (float) $stat['conversions'].' %';
                        }
                    }
                }
            }
        }

        $recentOffers = Offer::whereHas('clicks', function($query) {
            $query->orderBy('updated_at', 'desc');
        })->limit(5)->get();


        return view('admin.general.control', compact('content', 'offers', 'recentOffers'));
    }

    public function clearlead(Request $request)
    {
        $offer_id = $request->input('offer_id');

        if ($offer = Offer::find($offer_id)) {
            Click::where('offer_id', $offer_id)->update(['click_ip' => '10.0.2.2']);
            flash('Clear IP Lead success!');
            return redirect('admin/offers');
        } else {
            flash('No offer found!');
            return redirect('admin/offers');
        }
    }

    public function statistic($content, Request $request)
    {
        $clicks = null;

        $start = ($request->input('start')) ? $request->input('start') : '2016-01-01';
        $end = ($request->input('end')) ? $request->input('end') : '2016-12-31';

        switch ($content) {
            case "group" :

                $userIds = User::where('group_id', $request->input('content_id'))->pluck('id')->all();
                $clicks = DB::table('clicks')->whereIn('user_id', $userIds)
                    ->whereBetween(DB::raw("DATE_FORMAT(click_time, '%Y-%m-%d')"), [$start, $end]);

                break;
            case "user" :
                $userId = User::where('username', $request->input('content_id'))->first()->id;
                $clicks = DB::table('clicks')->where('user_id', $userId)
                    ->whereBetween(DB::raw("DATE_FORMAT(click_time, '%Y-%m-%d')"), [$start, $end]);

                break;
            case "offer" :
                $clicks = ($request->input('content_id')) ? DB::table('clicks')->where('offer_id', $request->input('content_id')) : DB::table('clicks');
                $clicks = $clicks->whereBetween(DB::raw("DATE_FORMAT(click_time, '%Y-%m-%d')"), [$start, $end]);
                break;
        }

        $totalClicks = $clicks->count();
        $query = clone $clicks;
        $records = $query->groupBy('offer_id')
            ->select(DB::raw('count(*) as total'), 'offer_id')
            ->get();

        $totalMoney = 0;

        if ($records->count() > 0) {
           foreach ($records as $record) {
               $offer = Offer::find($record->offer_id);
               $totalMoney += $record->total * $offer->click_rate;
           }
        }

        $clicks = $clicks
            ->join('offers', 'clicks.offer_id', '=', 'offers.id')
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))
            ->paginate(10);

        $title = 'Thống kê theo '.strtoupper($content).' từ ngày '.$start .' đến ngày '.$end;

        return view('admin.result', compact('clicks', 'totalMoney', 'totalClicks', 'title'));
    }

}
