<?php

namespace App\Http\Controllers\Frontend;


use App\Click;
use App\Http\Controllers\Controller;
use App\NetworkClick;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use App\MediaOffer;

class MainController extends Controller
{

    //return array ip and isoCode if have.
    //https://github.com/Torann/laravel-geoip

    private function checkIpAndLocation($offer, $request)
    {
        $offer_locations = trim(strtoupper($offer->geo_locations));
        if (!$offer_locations || ($offer_locations == 'ALL')) {
            return true;
        }

        if (strpos($offer_locations, 'GB') !== false) {
            $offer_locations .= ',UK';
        }

        if (strpos($offer_locations, 'UK') !== false) {
            $offer_locations .= ',GB';
        }

        $isoCode = null;
        $ipLocation = $request->ip();


        try {
            $reader = new Reader(storage_path('app/geoip.mmdb'));
            $isoCode = $reader->country($ipLocation)->country->isoCode;
        } catch (AddressNotFoundException $e) {
            return  ($ipLocation == '10.0.2.2');
        }  catch (\Exception $e) {
            return false;
        }

        if (strpos($offer_locations, $isoCode) !== false) {
            return true;
        } else {
            return false;
        }
    }

    private function checkDeviceOffer($offer)
    {

        //not check all

        if ($offer->allow_devices == 1) {
           return true;
        }

        $agent = new Agent();

        //mobile : include phone and tablets.

        if ($offer->allow_devices == 2 && !$agent->isMobile()) {
            return false;
        }

        //desktop

        if ($offer->allow_devices == 3 && !$agent->isDesktop()) {
            return false;
        }

        //Android mobile.

        if ($offer->allow_devices == 4 && ! ($agent->isMobile() && $agent->isAndroidOS()) ) {
            return false;
        }

        //IOS Mobile.

        if ($offer->allow_devices == 5 && ! ($agent->isMobile() && $agent->isiOS()) ) {
            return false;
        }

        if ($offer->allow_devices == 6 && ! ($agent->isPhone() && $agent->isiOS()) ) {
            return false;
        }

        if ($offer->allow_devices == 7 && ! ($agent->isTablet() && $agent->isiOS()) ) {
            return false;
        }

        return true;
    }

    public function index()
    {
        return view('welcome');
    }

    public function camp(Request $request)
    {
        $offer_id = (int) $request->input('offer_id');
        $user_id = (int) $request->input('user_id');

        if ($offer_id && $user_id) {

            $offer = Offer::find($offer_id);

            if ($offer && $offer->status) {

                $user = User::find($user_id);

                if ($user && $user->status) {

                    //check devices.

                    $checkDevices = $this->checkDeviceOffer($offer);
                    if ($checkDevices) {
                        $checkLocation = $this->checkIpAndLocation($offer, $request);

                        if ($checkLocation) {
                            //check if this ip click is existed in database or not.
                            $currentIp = $request->ip();

                            if ($offer->check_click_in_network) {
                                $count = DB::table('network_clicks')
                                    ->where('network_offer_id', $offer->net_offer_id)
                                    ->where('ip', $currentIp)
                                    ->count();
                            } else {
                                $count = DB::table('clicks')
                                    ->where('offer_id', $offer_id)
                                    ->where('click_ip', $currentIp)
                                    ->count();
                            }

                            if ($count == 0 || $offer->allow_multi_lead) {
                                //insert click and redirect
                                $hash_tag = md5(uniqid($offer_id.$user_id.$currentIp));
                                try {
                                    Click::create([
                                        'user_id' => $user_id,
                                        'offer_id' => $offer_id,
                                        'click_ip' => $currentIp,
                                        'click_time' => Carbon::now()->toDateTimeString(),
                                        'hash_tag' => $hash_tag
                                    ]);

                                    $redirect_link  = str_replace('#subid', $hash_tag, strtolower($offer->redirect_link));
                                    return redirect()->away($redirect_link);

                                } catch (\Exception $e) {
                                    return response()->json(['message' => 'Error happened when update database!']);
                                }
                            } else {
                                return response()->json(['message' => 'This ip already have click for this offer!']);
                            }
                        } else {
                            return  response()->json(['message' => 'Not allow Geo Locations!']);
                        }
                    } else {
                        return response()->json(['message' => 'Not allow devices!']);
                    }
                } else {
                    return response()->json(['message' => 'User is inactive or none existed!']);
                }
            } else {
              return response()->json(['message' => 'Offer is not active or none existed!']);
            }
        } else {
            return response()->json(['message' => 'Not enough parameters!']);
        }
    }

    public function postback(Request $request)
    {

        //http://bt.io/click?aid=65350&linkid=B159235&s1=&s2=&s3=&s4=&s5= CPAway
        //GET /postback?network_id=1&offer_id=198477&subid=9c5e22e270773205658d098b4e19b7d5&amount=0.44800000&status=1&ip=176.47.3.203&country=SA&tid=c74c18c82231ddd250d254fa0c35549c

        $network_id = $request->input('network_id');
        $offer_id = $request->input('offer_id');
        $sub_id = $request->input('subid');

        if ($network_id && $offer_id && $sub_id) {

            if ($request->input('network_id') != -1) {
                NetworkClick::create([
                    'network_id' => $network_id,
                    'network_offer_id' => $offer_id,
                    'sub_id' => $sub_id,
                    'amount' => $request->input('amount'),
                    'ip' => $request->input('ip')
                ]);
            }

        }

    }
    public function hashpostback(Request $request)
    {
        $sub_id = $request->input('subid');
        $network_id = $request->input('network_id');
        $findInClick = Click::where('hash_tag', $sub_id)->count();

        if ($findInClick > 0) {
            $click =  Click::where('hash_tag', $sub_id)->get()->first();
            $offer = Offer::find($click->offer_id);
            NetworkClick::create([
                'network_id' => $network_id,
                'network_offer_id' => $offer->net_offer_id,
                'sub_id' => $sub_id,
                'amount' => 0,
                'ip' => ''
            ]);
        }
    }

    public function xMedia()
    {
       $offers = MediaOffer::latest('updated_at')->paginate(100);

        foreach ($offers as $offer) {
            $hash_tag_1 = md5(uniqid($offer->offer_id.'s1'));
            $hash_tag_2 = md5(uniqid($offer->offer_id.'s2'));
            $hash_tag_3 = md5(uniqid($offer->offer_id.'s3'));

            $offer->hash_link = str_replace('&s1=&s2=&s3=', '&s1='.$hash_tag_1.'&s2='.$hash_tag_2.'&s3='.$hash_tag_3, $offer->offer_tracking_link);
        }

        return view('frontend.list_xmedia', compact('offers'));
    }

    public function recent(Request $request)
    {

        $checkTime =  Carbon::now()->tomorrow()->toDateString();

        if ($request->input('time') == $checkTime) {
            $siteRecentLead = DB::table('network_clicks')
                ->join('offers', 'network_clicks.network_offer_id', '=', 'offers.net_offer_id')
                ->join('networks', 'network_clicks.network_id', '=', 'networks.id')
                ->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->join('users', 'clicks.user_id', '=', 'users.id')
                ->select('offers.name', 'offers.id', 'clicks.created_at as click_at', 'network_clicks.ip', 'network_clicks.created_at', 'users.username', 'network_clicks.id as postback_id', 'network.name as network_name')
                ->orderBy('network_clicks.id', 'desc')
                ->limit(10)
                ->get();

            return view('admin.time', compact('siteRecentLead'));
        }

    }
}
