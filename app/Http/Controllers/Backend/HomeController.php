<?php

namespace App\Http\Controllers\Backend;

use App\Click;
use App\Group;
use App\Network;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HomeController extends AdminController
{

    protected function generateDashboard($userId = null)
    {

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $yesterdayStart = Carbon::now()->yesterday()->startOfDay();
        $yesterdayEnd = Carbon::now()->yesterday()->endOfDay();

        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();



        $initQuery = DB::table('network_clicks')
            ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->leftJoin('users', 'clicks.user_id', '=', 'users.id');

        if ($userId) {
            $initQuery = $initQuery->where('users.id', $userId);
        }

        //recent lead.


        $userRecent = clone $initQuery;

        $userRecent = $userRecent
            ->select('offers.name', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->orderBy('network_clicks.created_at', 'desc')
            ->limit(6)
            ->get();

        //money
        $moneyQuery = clone $initQuery;
        $moneyQuery = $moneyQuery->select(DB::raw("SUM(offers.click_rate) as total"));


        $todayMoneyQuery = clone $moneyQuery;
        $monthMoneyQuery = clone $moneyQuery;
        $totalMoneyQuery = clone $moneyQuery;

        $todayMoneyQuery = $todayMoneyQuery->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])->get();
        $monthMoneyQuery = $monthMoneyQuery->whereBetween('network_clicks.created_at', [$startMonth, $endMonth])->get();
        $totalMoneyQuery = $totalMoneyQuery->get();

        $content = [
            'today' => ($todayMoneyQuery->count() > 0) ? $todayMoneyQuery->first()->total : 0,
            'month' => ($monthMoneyQuery->count() > 0) ? $monthMoneyQuery->first()->total : 0,
            'total' => ($totalMoneyQuery->count() > 0) ? $totalMoneyQuery->first()->total : 0,
        ];

        //get offers.
        //api using to get real clicks.

        $apiData = [];

        if (!$userId) {
            $api_url = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=01&sm=01&sy=2016&ed='.
                Carbon::now()->day .'&em='.Carbon::now()->month.'&ey='.Carbon::now()->year;

            $stats = json_decode(file_get_contents($api_url), true);
            $apiData[1] = isset($stats['stats_summary']) ? $stats['stats_summary'] : [];
        }

        $offerQuery = clone $initQuery;

        $offerQuery = $offerQuery
            ->select(DB::raw("COUNT(network_clicks.id) as totalLeads, offers.id"))
            ->groupBy('offers.id');


        $todayOfferQuery = clone $offerQuery;
        $yesterdayOfferQuery = clone $offerQuery;
        $weekOfferQuery = clone $offerQuery;

        $todayOfferQuery = $todayOfferQuery->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])->get();
        $yesterdayOfferQuery = $yesterdayOfferQuery->whereBetween('network_clicks.created_at', [$yesterdayStart, $yesterdayEnd])->get();
        $weekOfferQuery = $weekOfferQuery->whereBetween('network_clicks.created_at', [$startWeek, $endWeek])->get();



        $todayOffers = [];
        $yesterdayOffers = [];
        $weekOffers = [];

        foreach ($todayOfferQuery as $offerSection) {

            $offer = Offer::find($offerSection->id);
            $site_click = $offer->clicks->count();
            $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads/$site_click)*100, 2).'%' : 'Not Available';
            $net_click = 0;

            if (isset($apiData[$offer->network_id])) {
                foreach ($apiData[$offer->network_id] as $stat) {
                    if (intval($stat['id']) == $offer->net_offer_id) {
                        $net_click = $stat['clicks'];
                    }
                }
            }

            $todayOffers[] = [
                'offer_name' => $offer->name,
                'net_click' => $net_click,
                'net_lead' => $offerSection->totalLeads,
                'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads/$net_click)*100, 2).'%' : 'Not Available',
                'site_cr' => $site_cr,
                'site_click' => $site_click,
            ];
        }

        foreach ($yesterdayOfferQuery as $offerSection) {

            $offer = Offer::find($offerSection->id);
            $site_click = $offer->clicks->count();
            $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads/$site_click)*100, 2).'%' : 'Not Available';
            $net_click = 0;

            if (isset($apiData[$offer->network_id])) {
                foreach ($apiData[$offer->network_id] as $stat) {
                    if (intval($stat['id']) == $offer->net_offer_id) {
                        $net_click = $stat['clicks'];
                    }
                }
            }

            $yesterdayOffers[] = [
                'offer_name' => $offer->name,
                'net_click' => $net_click,
                'net_lead' => $offerSection->totalLeads,
                'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads/$net_click)*100, 2).'%' : 'Not Available',
                'site_cr' => $site_cr,
                'site_click' => $site_click,
            ];
        }

        foreach ($weekOfferQuery as $offerSection) {

            $offer = Offer::find($offerSection->id);
            $site_click = $offer->clicks->count();
            $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads/$site_click)*100, 2).'%' : 'Not Available';
            $net_click = 0;

            if (isset($apiData[$offer->network_id])) {
                foreach ($apiData[$offer->network_id] as $stat) {
                    if (intval($stat['id']) == $offer->net_offer_id) {
                        $net_click = $stat['clicks'];
                    }
                }
            }

            $weekOffers[] = [
                'offer_name' => $offer->name,
                'net_click' => $net_click,
                'net_lead' => $offerSection->totalLeads,
                'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads/$net_click)*100, 2).'%' : 'Not Available',
                'site_cr' => $site_cr,
                'site_click' => $site_click,
            ];
        }

        return [$content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers];

    }


    public function index()
    {
        $currentUser = auth('backend')->user();
        $currentUserId = ($currentUser->id == 1) ? 12 : $currentUser->id;
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers) = $this->generateDashboard($currentUserId);
        return view('admin.index', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent'));
    }

    public function control()
    {
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers) = $this->generateDashboard();
        return view('admin.general.control', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent'));
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

    public function thongke()
    {

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'Choose Offer'] + Offer::pluck('name', 'id')->all();
        $globalNetworks = ['' => 'Choose Network'] + Network::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();
        
        return view('admin.result', compact('globalGroups', 'globalOffers', 'globalUsers', 'globalNetworks'));
    }

    public function statistic($content, Request $request)
    {
        $clicks = null;

        $start = ($request->input('start')) ? $request->input('start') : '2016-01-01';
        $end = ($request->input('end')) ? $request->input('end') : '2016-12-31';

        $queryStart = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $queryEnd = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

        $countTotal = null;

        $network_id = $request->input('network_id');

        switch ($content) {
            case "group" :
                $userIds = User::where('group_id', $request->input('content_id'))->pluck('id')->all();

                $clicks = DB::table('network_clicks')
                    ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))

                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                $clicks = $clicks->orderBy('network_clicks.created_at', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }
                $countTotal = $countTotal->get();


                break;
            case "user" :
                $userId = User::where('username', $request->input('content_id'))->first()->id;

                $clicks = DB::table('network_clicks')
                    ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))

                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                $clicks = $clicks->orderBy('network_clicks.created_at', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }
                $countTotal = $countTotal->get();

                break;
            case "offer" :
            $clicks = DB::table('network_clicks')
                ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))
                ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                ->orderBy('network_clicks.created_at', 'desc')
                ->paginate(10);

            $countTotal = DB::table('network_clicks')
                ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                ->get();

            break;

            case "network" :
                $clicks = DB::table('network_clicks')
                    ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('network_clicks.network_id', $request->input('content_id'))
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->orderBy('network_clicks.created_at', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('network_clicks.network_id', $request->input('content_id'))
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->get();

                break;
        }

        $customUrl = '/admin/statistic/'. $content.'?start='.$start.'&end='.$end;

        if ($request->input('content_id')) {
            $customUrl .= '&content_id='.$request->input('content_id');
        }
        if ($request->input('network_id')) {
            $customUrl .= '&network_id='.$request->input('network_id');
        }

        $clicks->setPath($customUrl);

        $totalClicks = $countTotal->first()->totalClicks;
        $totalMoney = $countTotal->first()->totalMoney;

        $title = 'Thống kê theo '.strtoupper($content).' từ ngày '.$start .' đến ngày '.$end;

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'Choose Offer'] + Offer::pluck('name', 'id')->all();
        $globalNetworks = ['' => 'Choose Network'] + Network::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();

        $content_id = $request->input('content_id') ? $request->input('content_id') : '';


        return view('admin.result', compact('clicks', 'totalMoney', 'totalClicks', 'title', 'globalGroups', 'globalOffers', 'globalNetworks', 'globalUsers', 'content', 'content_id', 'network_id', 'start', 'end'));
    }

}
