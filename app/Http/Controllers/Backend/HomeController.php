<?php

namespace App\Http\Controllers\Backend;

use App\Click;
use App\Offer;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HomeController extends AdminController
{

    public function index()
    {
       return view('admin.index');
    }

    public function control()
    {
        $content = [];

        $content['total_users'] = DB::table('users')->count();
        $content['active_users'] = DB::table('users')->where('status', true)->count();
        $content['total_clicks'] = DB::table('clicks')->count();
        $content['total_offers'] = DB::table('offers')->count();

        $today =  Carbon::now()->toDateString();
        $offers =  Offer::whereHas('clicks', function($query) use ($today) {
            $query->whereBetween('updated_at', [$today.' 00:00:00', $today.' 23:59:59']);
        })->get();

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

}
