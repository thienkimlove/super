<?php

namespace App\Http\Controllers\Backend;

use App\Functions;
use Illuminate\Http\Request;

class HomeController extends AdminController
{

    public function index()
    {
       //$response = Functions::apiGrab(['action' => 'stats_summary']);
       return view('admin.index');
    }

    public function listOffers(Request $request)
    {
        $options = [
            'action' => 'offers',
            'limit' => 10
        ];

        $searchOffer = null;

        $offers = [];

        if ($request->input('q')) {
            $searchOffer = $request->input('q');
            $options['keyword'] = $searchOffer;
        }

        if ($request->input('limit')) {
            $options['limit'] = $request->input('limit');
        }

        if ($options['limit'] == 1 && cache()->has('offers') && !$searchOffer) {
            $offers = cache()->get('offers');
        } else {

            $response = Functions::apiGrab($options);

            if (isset($response['offers'])) {
                $offers = $response['offers'];

                foreach ($offers as &$offer) {
                    $offer['offer_targeting'] = Functions::apiGrab(['action' => 'offer_targeting', 'id' =>
                        $offer['offer_id']]);
                    if ($offer['offer_targeting'] && is_array($offer['offer_targeting'])) {
                        $offer['offer_targeting'] = implode(', ', array_values($offer['offer_targeting']));
                    }

                    $offer['offer_countries'] = Functions::apiGrab(['action' => 'offer_countries', 'id' =>
                        $offer['offer_id']]);
                    if ($offer['offer_countries'] && is_array($offer['offer_countries'])) {
                        $offer['offer_countries'] = implode(', ', array_values($offer['offer_countries']));
                    }
                }

                if ($options['limit'] == 1 && !$searchOffer) {
                    cache()->put('offers', $offers, 1);
                }
            }
        }

        return view('admin.list_offers', compact('offers', 'searchOffer'));
    }

}
