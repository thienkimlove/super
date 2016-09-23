@extends('admin')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">{{$title}}</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">

                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>OfferId</th>
                                <th>Offer Name</th>
                                <th>User</th>
                                <th>IP</th>
                                <th>SubId</th>
                                <th>Device</th>
                                <th>Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clicks as $click)
                                <tr>
                                    <td>{{$click->id}}</td>
                                    <td>{{$click->offer_id}}</td>
                                    <td>{{$click->offer_name}}</td>
                                    <td>{{$click->username}}</td>
                                    <td>{{$click->click_ip}}</td>
                                    <td>{{$click->hash_tag}}</td>
                                    <td>{{config('devices')[$click->offer_allow_devices]}}</td>
                                    <td>{{$click->offer_geo_locations}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">{!!$clicks->render()!!}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">Total : {{$totalClicks}}</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">Total Money : {{$totalMoney}}</div>
                    </div>


                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

    </div>
@endsection