<?php namespace App\Http\Controllers\Backend;

use App\Http\Requests\OfferRequest;
use App\Offer;
use Illuminate\Http\Request;


class OffersController extends AdminController
{
    public $devices;

    public function __construct()
    {
        parent::__construct();

        $this->devices = [
            '' => 'Choose offer allow devices'
        ];

        foreach (config('devices') as $key =>  $device) {
            $this->devices[$key] = $device;
        }
    }

    public function index(Request $request)
    {
        $searchOffer = null;

        $offers = Offer::latest('updated_at');

        //if normal user we show only offer user has click on.

        $user = auth('backend')->user();

       /* if ($user->permission_id == 3) {
            $offers = $offers->whereHas('clicks', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }*/

        if ($request->input('q')) {
            $searchOffer = urldecode($request->input('q'));
            $offers = $offers->where('name', 'LIKE', '%'. $searchOffer. '%');
        }

        $offers = $offers->paginate(10);

        return view('admin.offer.index', compact('offers', 'searchOffer'));
    }

    public function create()
    {
        $devices = $this->devices;
        return view('admin.offer.form', compact('devices'));
    }

    public function store(OfferRequest $request)
    {

        try {

            Offer::create([
                'name' => $request->input('name'),
                'redirect_link' => $request->input('redirect_link'),
                'click_rate' => $request->input('click_rate'),
                'geo_locations' => $request->input('geo_locations'),
                'allow_devices' => $request->input('allow_devices'),
                'status' => ($request->input('status') == 'on') ? true : false
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Create offer success!', 'success');
        return redirect('admin/offers');
    }


    public function edit($id)
    {
        $devices = $this->devices;
        $offer = Offer::find($id);
        return view('admin.offer.form', compact('devices', 'offer'));
    }


    public function update($id, OfferRequest $request)
    {
        $offer = Offer::find($id);


        $data = [
            'name' => $request->input('name'),
            'redirect_link' => $request->input('redirect_link'),
            'click_rate' => $request->input('click_rate'),
            'geo_locations' => $request->input('geo_locations'),
            'allow_devices' => $request->input('allow_devices'),
            'status' => ($request->input('status') == 'on') ? true : false
        ];

        try {
            $offer->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Update offer success!', 'success');
        return redirect('admin/offers');
    }


    public function destroy($id)
    {
        Offer::find($id)->delete();
        flash('Success deleted offer!');
        return redirect('admin/offers');
    }

}
