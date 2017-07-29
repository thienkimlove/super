<?php

namespace App\Http\Controllers\Frontend;


use App\Click;
use App\Http\Controllers\Controller;
use App\NetworkClick;
use App\Offer;
use App\User;
use App\VirtualLog;
use Carbon\Carbon;
use DB;
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
        if (env('NO_CHECK_IP')) {
            return 'US';
        }

        $offer_locations = trim(strtoupper($offer->geo_locations));
        if (!$offer_locations || ($offer_locations == 'ALL')) {
            return 'US';
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
            $ipInformation = file_get_contents('http://freegeoip.net/json/'.$ipLocation);
            $address = json_decode($ipInformation, true);
            $isoCode = $address['country_code'];
        } catch (\Exception $e) {
            \Log::error('check geo ip error='.$e->getMessage());
            try {
                $getIp = \GeoIP::getLocation($ipLocation);
                $isoCode = $getIp['isoCode'];
            } catch (AddressNotFoundException $e) {
                return  ($ipLocation == '10.0.2.2');
            }  catch (\Exception $e) {
                \Log::error('check geo ip error='.$e->getMessage());
                return false;
            }
        }

        if (strpos($offer_locations, $isoCode) !== false) {
            return $isoCode;
        } else {
            \Log::error('offer_id='.$offer->id.'and offer_locations='.$offer_locations.' but ip='.$ipLocation.' and isoCode='.$isoCode);
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
                                   $addedClick = Click::create([
                                        'user_id' => $user_id,
                                        'offer_id' => $offer_id,
                                        'click_ip' => $currentIp,
                                        'click_time' => Carbon::now()->toDateTimeString(),
                                        'hash_tag' => $hash_tag
                                    ]);

                                    $redirect_link  = str_replace('#subId', $hash_tag, $offer->redirect_link);
                                    $redirect_link  = str_replace('#subid', $hash_tag, $redirect_link);

                                    #put in queues for process multi click.
                                    try {
                                        $numberOfVirtualClicks = ($offer->virtual_clicks)? 10 : 30;
                                        for ($i = 0; $i < $numberOfVirtualClicks; $i++) {
                                            VirtualLog::create([
                                                'offer_id' => $offer_id,
                                                'click_id' => $addedClick->id,
                                                'user_country' => $checkLocation,
                                                'redirect_link' => str_replace('#subId', '', $offer->redirect_link)
                                            ]);
                                        }
                                    } catch (\Exception $e) {

                                    }


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

            if ($request->input('status') != -1) {

                $checkExistedLead = NetworkClick::where('network_id', $network_id)
                    ->where('network_offer_id', $offer_id)
                    ->where('sub_id', $sub_id)
                    ->count();
                if ($checkExistedLead == 0) {
                   $networkClick = NetworkClick::create([
                        'network_id' => $network_id,
                        'network_offer_id' => $offer_id,
                        'sub_id' => $sub_id,
                        'amount' => $request->input('amount'),
                        'ip' => $request->input('ip')
                    ]);

                   $offer = Offer::find($offer_id);

                   if (!$offer->virtual_clicks) {
                       #put in queues for process multi click.
                       try {
                           $numberOfVirtualClicks = 30;
                           $checkLocation = null;
                           $offer_locations = trim(strtoupper($offer->geo_locations));
                           if (!$offer_locations || ($offer_locations == 'ALL')) {
                               $checkLocation = 'us';
                           } elseif (strpos($offer_locations, 'GB') !== false) {
                               $checkLocation = 'uk';
                           } else {
                               $offer_locations = explode(',', $offer_locations);
                               $checkLocation = trim(strtolower($offer_locations[0]));
                           }

                           for ($i = 0; $i < $numberOfVirtualClicks; $i++) {
                               VirtualLog::create([
                                   'offer_id' => $offer->id,
                                   'network_click_id' => $networkClick->id,
                                   'user_country' => $checkLocation,
                                   'redirect_link' => str_replace('#subId', '', $offer->redirect_link)
                               ]);
                           }
                       } catch (\Exception $e) {

                       }
                   }
                }
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

            $checkExistedLead = NetworkClick::where('network_id', $network_id)
                ->where('network_offer_id', $offer->net_offer_id)
                ->where('sub_id', $sub_id)
                ->count();
            if ($checkExistedLead == 0) {
                $networkClick = NetworkClick::create([
                    'network_id' => $network_id,
                    'network_offer_id' => $offer->net_offer_id,
                    'sub_id' => $sub_id,
                    'amount' => $request->input('amount') ? $request->input('amount') : 0,
                    'ip' => $click->click_ip
                ]);

                if (!$offer->virtual_clicks) {
                    #put in queues for process multi click.
                    try {
                        $numberOfVirtualClicks = 30;
                        $checkLocation = null;
                        $offer_locations = trim(strtoupper($offer->geo_locations));
                        if (!$offer_locations || ($offer_locations == 'ALL')) {
                            $checkLocation = 'us';
                        } elseif (strpos($offer_locations, 'GB') !== false) {
                            $checkLocation = 'uk';
                        } else {
                            $offer_locations = explode(',', $offer_locations);
                            $checkLocation = trim(strtolower($offer_locations[0]));
                        }

                        for ($i = 0; $i < $numberOfVirtualClicks; $i++) {
                            VirtualLog::create([
                                'offer_id' => $offer->id,
                                'network_click_id' => $networkClick->id,
                                'user_country' => $checkLocation,
                                'redirect_link' => str_replace('#subId', '', $offer->redirect_link)
                            ]);
                        }
                    } catch (\Exception $e) {

                    }
                }
            }
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

}
