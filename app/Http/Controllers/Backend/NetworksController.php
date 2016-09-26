<?php namespace App\Http\Controllers\Backend;

use App\Http\Requests\NetworkRequest;
use App\Network;

class NetworksController extends AdminController
{

    public function index()
    {

        $networks = Network::latest('updated_at')->paginate(10);

        return view('admin.network.index', compact('networks'));
    }

    public function create()
    {
        return view('admin.network.form');
    }

    public function store(NetworkRequest $request)
    {

        try {

            Network::create([
                'name' => $request->input('name')
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Create network success!', 'success');
        return redirect('admin/networks');
    }


    public function edit($id)
    {
        $network = Network::find($id);
        return view('admin.network.form', compact('network'));
    }


    public function update($id, NetworkRequest $request)
    {
        $network = Network::find($id);


        $data = [
            'name' => $request->input('name')
        ];

        try {
            $network->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Update network success!', 'success');
        return redirect('admin/networks');
    }


    public function destroy($id)
    {
        Network::find($id)->delete();
        flash('Success deleted network!');
        return redirect('admin/networks');
    }

}