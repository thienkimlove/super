<?php

namespace App\Http\Controllers\Backend;

use App\Click;
use App\Group;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HomeController extends AdminController
{

    protected function getApiData($offer, $apiData)
    {

        $data = [
            'offer_name' => $offer->name,
            'net_click' => 0,
            'net_lead' => 0,
            'net_cr' => null,
            'site_cr' => null,
            'site_click' => $offer->clicks->count(),
        ];

        if (isset($apiData[$offer->network_id]) &&  $apiData[$offer->network_id]) {
            foreach ($apiData[$offer->network_id] as $stat) {
                if (intval($stat['id']) == $offer->net_offer_id) {
                    $data['net_click'] = $stat['clicks'];
                    $data['net_lead'] = $stat['leads'];
                    $data['site_cr'] = ($data['site_click'] > 0) ? round((intval($data['net_lead'])/$data['site_click'])*100, 2) .'%' : 'Not Available';
                    $data['net_cr'] = intval($stat['conversions']).'%';
                }
            }
        }
        return $data;
    }

    protected function getOffers($time, $apiData, $currentUserId = null)
    {

        $start = null;
        $end = null;
        $apiUrl = null;

        if ($time == 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } else if ($time == 'yesterday') {
            $start = Carbon::now()->yesterday()->startOfDay();
            $end = Carbon::now()->yesterday()->endOfDay();
        } else {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        }

        $offers = Offer::whereHas('clicks', function($query) use ($currentUserId, $start, $end) {
            if ($currentUserId) {
                $query->whereBetween('updated_at', [$start, $end])
                    ->where('user_id', $currentUserId);
            } else {
                $query->whereBetween('updated_at', [$start, $end]);
            }

        })->get();

        $data = [];

        foreach ($offers as $offer) {
            $data[$offer->id] = $this->getApiData($offer, $apiData);
        }
        return $data;

    }

    protected function getMoney($time = null, $currentUserId = null)
    {
        $start = null;
        $end = null;
        $money = 0;

        if ($time == 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } else if ($time == 'month') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        }

        $query = DB::table('network_clicks')
            ->select(DB::raw("SUM(offers.click_rate) as total"))
            ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->leftJoin('users', 'clicks.user_id', '=', 'users.id');



        if ($start && $end) {
            $query = $query->whereBetween('network_clicks.created_at', [$start, $end]);
        }

        if ($currentUserId) {
            $query = $query->where('users.id', $currentUserId);
        }

        $query = $query->get();


        if ($query->count() > 0) {
            $money = $query->first()->total;
        }

        return $money;
    }


    public function index()
    {
        $currentUser = auth('backend')->user();
        $currentUserId = ($currentUser->id == 1) ? 12 : $currentUser->id;

        $content = [
            'today' => $this->getMoney('today', $currentUserId),
            'month' => $this->getMoney('month', $currentUserId),
            'total' => $this->getMoney(null, $currentUserId),
        ];

        $apiData = [];

        $todayOffers =  $this->getOffers('today', $apiData, $currentUserId);
        $yesterdayOffers =  $this->getOffers('yesterday', $apiData, $currentUserId);
        $weekOffers =  $this->getOffers('week', $apiData, $currentUserId);

        $userRecent = DB::table('network_clicks')
            ->select('offers.name', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
            ->where('users.id', $currentUserId)
            ->orderBy('network_clicks.created_at', 'desc')
            ->limit(6)
            ->get();

        return view('admin.index', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent'));
    }

    public function control()
    {
        $content = [
            'today' => $this->getMoney('today'),
            'month' => $this->getMoney('month'),
            'total' => $this->getMoney(),
        ];


        $apiData = [];

        $now = Carbon::now();

        $api_url = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=01&sm=01&sy=2016&ed='.
            $now->day .'&em='.$now->month.'&ey='.$now->year;

        $stats = json_decode(file_get_contents($api_url), true);
        $apiData[1] = isset($stats['stats_summary']) ? $stats['stats_summary'] : [];


        $todayOffers =  $this->getOffers('today', $apiData);
        $yesterdayOffers =  $this->getOffers('yesterday', $apiData);
        $weekOffers =  $this->getOffers('week', $apiData);


        $userRecent = DB::table('network_clicks')
            ->select('offers.name', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
            ->orderBy('network_clicks.created_at', 'desc')
            ->limit(6)
            ->get();

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
        $globalOffers = ['' => 'All Offer'] + Offer::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();
        return view('admin.result', compact('globalGroups', 'globalOffers', 'globalUsers'));
    }

    public function statistic($content, Request $request)
    {
        $clicks = null;

        $start = ($request->input('start')) ? $request->input('start') : '2016-01-01';
        $end = ($request->input('end')) ? $request->input('end') : '2016-12-31';

        $queryStart = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $queryEnd = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

        $countTotal = null;

        switch ($content) {
            case "group" :
                $userIds = User::where('group_id', $request->input('content_id'))->pluck('id')->all();

                $clicks = DB::table('network_clicks')
                    ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))

                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->orderBy('network_clicks.created_at', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->get();


                break;
            case "user" :
                $userId = User::where('username', $request->input('content_id'))->first()->id;

                $clicks = DB::table('network_clicks')
                    ->select('clicks.id', 'clicks.offer_id', 'clicks.click_ip', 'clicks.hash_tag', DB::raw('offers.name as offer_name'), DB::raw('users.username as username'), DB::raw('offers.allow_devices as offer_allow_devices'), DB::raw('offers.geo_locations as offer_geo_locations'))

                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->orderBy('network_clicks.created_at', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->leftJoin('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->leftJoin('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->leftJoin('users', 'clicks.user_id', '=', 'users.id')
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd])
                    ->get();

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
        }

        $customUrl = '/admin/statistic/'. $content.'?start='.$start.'&end='.$end;

        if ($request->input('content_id')) {
            $customUrl .= '&content_id='.$request->input('content_id');
        }

        $clicks->setPath($customUrl);

        $totalClicks = $countTotal->first()->totalClicks;
        $totalMoney = $countTotal->first()->totalMoney;

        $title = 'Thống kê theo '.strtoupper($content).' từ ngày '.$start .' đến ngày '.$end;

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'All Offer'] + Offer::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();

        $content_id = $request->input('content_id') ? $request->input('content_id') : '';


        return view('admin.result', compact('clicks', 'totalMoney', 'totalClicks', 'title', 'globalGroups', 'globalOffers', 'globalUsers', 'content', 'content_id', 'start', 'end'));
    }

}
