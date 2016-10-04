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

        $apiData = [];

        $todayOffers =  $this->getOffers('today', $apiData, $currentUser->id);

        $yesterdayOffers =  $this->getOffers('yesterday', $apiData, $currentUser->id);
        $weekOffers =  $this->getOffers('week', $apiData, $currentUser->id);

        $recentOffers = Offer::whereHas('clicks', function($query) use ($currentUser) {
            $query->orderBy('updated_at', 'desc')
                ->where('user_id', $currentUser->id);
        })->limit(5)->get();

        return view('admin.index', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'recentOffers'));
    }

    public function control()
    {
        $content = [];

        $content['total_users'] = DB::table('users')->count();
        $content['active_users'] = DB::table('users')->where('status', true)->count();
        $content['total_clicks'] = DB::table('clicks')->count();
        $content['total_offers'] = DB::table('offers')->count();

        $apiData = [];

        $now = Carbon::now();

        $api_url = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=01&sm=01&sy=2016&ed='.
            $now->day .'&em='.$now->month.'&ey='.$now->year;

        $stats = json_decode(file_get_contents($api_url), true);
        $apiData[1] = isset($stats['stats_summary']) ? $stats['stats_summary'] : [];


        $todayOffers =  $this->getOffers('today', $apiData);
        $yesterdayOffers =  $this->getOffers('yesterday', $apiData);
        $weekOffers =  $this->getOffers('week', $apiData);


        $recentOffers = Click::latest('updated_at')->get();

        $userRecent = [];

        $i = 0;

        foreach ($recentOffers as $offer) {

            if ($i < 6 && !isset($userRecent[$offer->click_ip])) {
                $i ++;
                $userRecent[$offer->click_ip] = [
                    'username' => $offer->user->username,
                    'offer_name' => $offer->offer->name,
                    'time' => $offer->click_time
                ];
            } else {
                if (isset($userRecent[$offer->click_ip]) && $userRecent[$offer->click_ip]['time'] < $offer->click_time) {
                    $userRecent[$offer->click_ip]['time'] = $offer->click_time;
                }
            }
        }


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

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'All Offer'] + Offer::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();

        return view('admin.result', compact('clicks', 'totalMoney', 'totalClicks', 'title', 'globalGroups', 'globalOffers', 'globalUsers'));
    }

}
