<?php namespace App\Http\Controllers\Backend;

use App\Http\Requests\OfferRequest;
use App\Network;
use App\Offer;
use Illuminate\Http\Request;


class OffersController extends AdminController
{
    public $devices;
    public $networks;

    public function __construct()
    {
        parent::__construct();

        $this->devices = [
        '' => 'Choose offer allow devices'
    ];

        foreach (config('devices') as $key =>  $device) {
            $this->devices[$key] = $device;
        }

        $this->networks = [
            '' => 'Choose network'
        ] + Network::pluck('name', 'id')->all();
    }

    public function index(Request $request)
    {
        $searchOffer = null;
        $searchCountry = null;
        $searchDevice = null;

        $offers = Offer::latest('updated_at');



        if ($request->input('q')) {
            $searchOffer = urldecode($request->input('q'));
            $offers = $offers->where('name', 'LIKE', '%'. $searchOffer. '%');
        }

        if ($request->input('country')) {
            $searchCountry = urldecode($request->input('country'));
            $offers = $offers->where('geo_locations', 'LIKE', '%'. $searchCountry. '%');
        }

        if ($request->input('device')) {
            $searchDevice = urldecode($request->input('device'));
            $offers = $offers->where('allow_devices', $searchDevice);
        }

        if ($request->input('auto') && $request->input('auto') == 1) {
            $offers = $offers->where('auto', true)->paginate(10);
            $offers->setPath('/admin/offers?auto=1');
        } else {
            $offers = $offers->where('auto', false)->paginate(10);
        }
        $auto = ($request->input('auto') == 1) ? 1 : 0;
        $devices = $this->devices;
        return view('admin.offer.index', compact('offers', 'searchOffer', 'auto', 'searchCountry', 'devices', 'searchDevice'));
    }


    public function create()
    {
        $devices = $this->devices;
        $networks = $this->networks;
        return view('admin.offer.form', compact('devices', 'networks'));
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
                'network_id' => $request->input('network_id'),
                'net_offer_id' => $request->input('net_offer_id'),
                'status' => ($request->input('status') == 'on') ? true : false,
                'allow_multi_lead' => ($request->input('allow_multi_lead') == 'on') ? true : false,
                'check_click_in_network' => ($request->input('check_click_in_network') == 'on') ? true : false,
                'image' => $request->input('image')
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
        $networks = $this->networks;
        $offer = Offer::find($id);
        return view('admin.offer.form', compact('devices', 'offer', 'networks'));
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
            'network_id' => $request->input('network_id'),
            'net_offer_id' => $request->input('net_offer_id'),
            'image' => $request->input('image'),
            'status' => ($request->input('status') == 'on') ? true : false,
            'allow_multi_lead' => ($request->input('allow_multi_lead') == 'on') ? true : false,
            'check_click_in_network' => ($request->input('check_click_in_network') == 'on') ? true : false,
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
