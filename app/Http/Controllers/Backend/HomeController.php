<?php

namespace App\Http\Controllers\Backend;

class HomeController extends AdminController
{

    public function index()
    {
       return view('admin.index');
    }

}
