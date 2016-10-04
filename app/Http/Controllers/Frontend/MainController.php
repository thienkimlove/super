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

                            $count = DB::table('clicks')
                                ->where('offer_id', $offer_id)
                                ->where('click_ip', $currentIp)
                                ->count();
                            if ($count == 0) {
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
