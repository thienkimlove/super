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
            ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->join('users', 'clicks.user_id', '=', 'users.id');

        if ($userId) {
            $initQuery = $initQuery->where('users.id', $userId);
        }

        //recent lead.


        $userRecent = clone $initQuery;

        $userRecent = $userRecent
            ->select('offers.name', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->orderBy('network_clicks.id', 'desc')
            ->limit(10)
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

        $api_url_today = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=' .
            Carbon::now()->day . '&sm=' . Carbon::now()->month . '&sy=' . Carbon::now()->year .
            '&ed=' . Carbon::now()->day . '&em=' . Carbon::now()->month . '&ey=' . Carbon::now()->year;

        $today_stats = json_decode(@file_get_contents($api_url_today), true);

        $api_url_yesterday = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=' .
            Carbon::now()->yesterday()->day . '&sm=' . Carbon::now()->yesterday()->month . '&sy=' . Carbon::now()->yesterday()->year .
            '&ed=' . Carbon::now()->yesterday()->day . '&em=' . Carbon::now()->yesterday()->month . '&ey=' . Carbon::now()->yesterday()->year;

        $yesterday_stats = json_decode(@file_get_contents($api_url_yesterday), true);

        $api_url_week = 'http://bt.io/apiv2/?key=2b52b92affc0cdecb8f32ee29d901835&action=stats_summary&sd=' .
            Carbon::now()->startOfWeek()->day . '&sm=' . Carbon::now()->startOfWeek()->month . '&sy=' . Carbon::now()->startOfWeek()->year .
            '&ed=' . Carbon::now()->endOfWeek()->day . '&em=' . Carbon::now()->endOfWeek()->month . '&ey=' . Carbon::now()->endOfWeek()->year;

        $week_stats = json_decode(@file_get_contents($api_url_week), true);


        $apiData[1] = [
            'today' => isset($today_stats['stats_summary']) ? $today_stats['stats_summary'] : [],
            'yesterday' => isset($yesterday_stats['stats_summary']) ? $yesterday_stats['stats_summary'] : [],
            'week' => isset($week_stats['stats_summary']) ? $week_stats['stats_summary'] : [],
        ];

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

        if ($todayOfferQuery->count() > 0) {
            foreach ($todayOfferQuery as $offerSection) {
                $offer = Offer::find($offerSection->id);
                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$todayStart, $todayEnd])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';
                    $net_click = 0;

                    if (isset($apiData[$offer->network_id])) {
                        foreach ($apiData[$offer->network_id]['today'] as $stat) {
                            if (intval($stat['id']) == $offer->net_offer_id) {
                                $net_click = $stat['clicks'];
                            }
                        }
                    }

                    $todayOffers[] = [
                        'offer_name' => $offer->name,
                        'net_click' => $net_click,
                        'net_lead' => $offerSection->totalLeads,
                        'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads / $net_click) * 100, 2) . '%' : 'Not Available',
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }


        if ($yesterdayOfferQuery->count() > 0) {
            foreach ($yesterdayOfferQuery as $offerSection) {

                $offer = Offer::find($offerSection->id);
                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';
                    $net_click = 0;

                    if (isset($apiData[$offer->network_id])) {
                        foreach ($apiData[$offer->network_id]['yesterday'] as $stat) {
                            if (intval($stat['id']) == $offer->net_offer_id) {
                                $net_click = $stat['clicks'];
                            }
                        }
                    }

                    $yesterdayOffers[] = [
                        'offer_name' => $offer->name,
                        'net_click' => $net_click,
                        'net_lead' => $offerSection->totalLeads,
                        'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads / $net_click) * 100, 2) . '%' : 'Not Available',
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }

        if ($weekOfferQuery->count() > 0) {
            foreach ($weekOfferQuery as $offerSection) {

                $offer = Offer::find($offerSection->id);

                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$startWeek, $endWeek])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';
                    $net_click = 0;

                    if (isset($apiData[$offer->network_id])) {
                        foreach ($apiData[$offer->network_id]['week'] as $stat) {
                            if (intval($stat['id']) == $offer->net_offer_id) {
                                $net_click = $stat['clicks'];
                            }
                        }
                    }

                    $weekOffers[] = [
                        'offer_name' => $offer->name,
                        'net_click' => $net_click,
                        'net_lead' => $offerSection->totalLeads,
                        'net_cr' => ($net_click > 0) ? round(($offerSection->totalLeads / $net_click) * 100, 2) . '%' : 'Not Available',
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }

        return [$content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers];

    }


    public function index()
    {
        $currentUser = auth('backend')->user();
        $currentUserId = ($currentUser->id == 1) ? 12 : $currentUser->id;
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers) = $this->generateDashboard($currentUserId);
        return view('admin.general.control', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent', 'currentUserId'));
    }

    public function ajaxSiteRecentLead()
    {
        $siteRecentLead = DB::table('network_clicks')
            ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
            ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->select('offers.name','offers.id', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->orderBy('network_clicks.id', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'html' => view('admin.ajax_recent_lead', compact('siteRecentLead'))->render()]);
    }

    public function control()
    {
        $currentUserId = null;
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers) = $this->generateDashboard();
        return view('admin.general.control', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent', 'currentUserId'));
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

    public function ajax($content, Request $request)
    {
        if ($content == 'user') {
            $records = User::where('username', 'like', '%' . $request->input('q'). '%')->get();
            $response = [];
            foreach ($records as $record) {
                $response[] = ['id' => $record->id, 'name' => $record->username];
            }
            return response()->json($response);
        }

        if ($content == 'offer') {
            $records = Offer::where('name', 'like', '%' . $request->input('q'). '%')->where('auto', false)->get();
            $response = [];
            foreach ($records as $record) {
                $response[] = ['id' => $record->id, 'name' => $record->name];
            }
            return response()->json($response);
        }
    }

    public function thongke()
    {

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        //chi hien thi danh sach cac offer co lead.
        $globalOffers = ['' => 'Choose Offer'] + Offer::has('network_clicks')->pluck('name', 'id')->all();
        foreach ($globalOffers as $key => $value) {
            if ($key) {
                $globalOffers[$key] = $value.' ID='.$key;
            }
        }
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

        $search_user = $request->input('search_user');
        $search_offer = $request->input('search_offer');

        $userSearchId = $request->input('search_user_id');
        $offerSearchId = $request->input('search_offer_id');


        $displaySearchOffer = false;
        $displaySearchUser = false;

        switch ($content) {
            case "group" :
                $userIds = User::where('group_id', $request->input('content_id'))->pluck('id')->all();

                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )

                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }

                if ($userSearchId) {
                    $countTotal = $countTotal->where('users.id', $userSearchId);
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchOffer = true;
                $displaySearchUser =  true;

                break;
            case "user" :
                $userId = User::where('username', $request->input('content_id'))->first()->id;

                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )

                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                    //->where('offers.auto', false)
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                    //->where('offers.auto', false)
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchOffer = true;

                break;
            case "offer" :
            $clicks = DB::table('network_clicks')
                ->select(
                    'clicks.id',
                    'clicks.offer_id',
                    'clicks.click_ip',
                    'clicks.hash_tag',
                    DB::raw('offers.id as offer_site_id'),
                    DB::raw('offers.name as offer_name'),
                    DB::raw('users.username as username'),
                    DB::raw('offers.allow_devices as offer_allow_devices'),
                    DB::raw('offers.geo_locations as offer_geo_locations')
                )
                ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->join('users', 'clicks.user_id', '=', 'users.id')
                //->where('offers.auto', false)
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                ->paginate(10);

            $countTotal = DB::table('network_clicks')
                ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->join('users', 'clicks.user_id', '=', 'users.id')
               // ->where('offers.auto', false)
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
            if ($userSearchId) {
                $countTotal = $countTotal->where('users.id', $userSearchId);
            }

            $countTotal = $countTotal->get();
            $displaySearchUser = true;

            break;

            case "network" :
                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )
                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                if ($request->input('content_id')) {
                    $clicks = $clicks->where('network_clicks.network_id', $request->input('content_id'));
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }


                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                    ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $countTotal = $countTotal->where('users.id', $userSearchId);
                }

                if ($request->input('content_id')) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $request->input('content_id'));
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchUser = true;
                $displaySearchOffer = true;

                break;
        }

        $customUrl = '/admin/statistic/'. $content.'?start='.$start.'&end='.$end;

        if ($request->input('content_id')) {
            $customUrl .= '&content_id='.$request->input('content_id');
        }
        if ($request->input('network_id')) {
            $customUrl .= '&network_id='.$request->input('network_id');
        }

        if ($request->input('search_offer_id')) {
            $customUrl .= '&search_offer_id='.$request->input('search_offer_id');
        }

        if ($request->input('search_user_id')) {
            $customUrl .= '&search_user_id='.$request->input('search_user_id');
        }

        if ($request->input('search_offer')) {
            $customUrl .= '&search_offer='.$request->input('search_offer');
        }

        if ($request->input('search_user')) {
            $customUrl .= '&search_user='.$request->input('search_user');
        }


        $clicks->setPath($customUrl);

        $totalClicks = $countTotal->first()->totalClicks;
        $totalMoney = $countTotal->first()->totalMoney;

        $title = 'Thống kê theo '.strtoupper($content).' từ ngày '.$start .' đến ngày '.$end;

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'Choose Offer'] + Offer::has('network_clicks')->pluck('name', 'id')->all();
        foreach ($globalOffers as $key => $value) {
            if ($key) {
                $globalOffers[$key] = $value.' ID='.$key;
            }
        }
        $globalNetworks = ['' => 'Choose Network'] + Network::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();

        $content_id = $request->input('content_id') ? $request->input('content_id') : '';


        return view('admin.result', compact(
            'clicks',
            'totalMoney',
            'totalClicks',
            'title',
            'globalGroups',
            'globalOffers',
            'globalNetworks',
            'globalUsers',
            'content',
            'content_id',
            'network_id',
            'start',
            'end',
            'search_user',
            'search_offer',
            'displaySearchUser',
            'displaySearchOffer',
            'offerSearchId',
            'userSearchId'
        ));
    }

}
