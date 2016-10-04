@extends('admin')

@section('content')

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-comments fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['total_users']}}</div>
                            <div>Total Users</div>
                        </div>
                    </div>
                </div>
                <a href="{{url('admin/users/index')}}">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['active_users']}}</div>
                            <div>Total Active Users!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['total_clicks']}}</div>
                            <div>Total Clicks!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>


        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-support fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['total_offers']}}</div>
                            <div>Total Offers!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @if ($todayOffers)
       <div class="row">
           <div class="col-lg-12">
               <!-- /.panel -->
               <div class="panel panel-default">

                   <div class="panel-heading">
                       <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy ngày hôm nay
                   </div>
                   <!-- /.panel-heading -->
                   <div class="panel-body">
                       <div class="row">
                           <div class="col-lg-12">
                               <div class="table-responsive">
                                   <table class="table table-bordered table-hover table-striped">
                                       <thead>
                                       <tr>
                                           <th>Name</th>
                                           <th>Clicks</th>
                                           <th>Lead</th>
                                           <th>CR</th>
                                           <th>Real Clicks</th>
                                           <th>Real CR</th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       @foreach ($todayOffers as $offer)
                                           <tr>
                                               <td>{{$offer['offer_name']}}</td>
                                               <td>{{$offer['site_click'] }}</td>
                                               <td>{{$offer['net_lead']}}</td>
                                               <td>{{ $offer['site_cr'] }}</td>
                                               <td>{{$offer['net_click']}}</td>
                                               <td>{{ $offer['net_cr'] }}</td>
                                           </tr>
                                       @endforeach
                                       </tbody>
                                   </table>
                               </div>
                               <!-- /.table-responsive -->
                           </div>
                           <!-- /.col-lg-4 (nested) -->

                           <!-- /.col-lg-8 (nested) -->
                       </div>
                       <!-- /.row -->
                   </div>
                   <!-- /.panel-body -->
               </div>            <!-- /.panel -->

               <!-- /.panel -->
           </div>
       </div>
    @endif

    @if ($yesterdayOffers)
        <div class="row">
            <div class="col-lg-12">
                <!-- /.panel -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy ngày hôm qua
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Clicks</th>
                                            <th>Lead</th>
                                            <th>CR</th>
                                            <th>Real Clicks</th>
                                            <th>Real CR</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($yesterdayOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['site_click'] }}</td>
                                                <td>{{$offer['net_lead']}}</td>
                                                <td>{{ $offer['site_cr'] }}</td>
                                                <td>{{$offer['net_click']}}</td>
                                                <td>{{ $offer['net_cr'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.col-lg-4 (nested) -->

                            <!-- /.col-lg-8 (nested) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>            <!-- /.panel -->

                <!-- /.panel -->
            </div>
        </div>
    @endif

    @if ($weekOffers)
        <div class="row">
            <div class="col-lg-12">
                <!-- /.panel -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy tuần này
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Clicks</th>
                                            <th>Lead</th>
                                            <th>CR</th>
                                            <th>Real Clicks</th>
                                            <th>Real CR</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($weekOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['site_click'] }}</td>
                                                <td>{{$offer['net_lead']}}</td>
                                                <td>{{ $offer['site_cr'] }}</td>
                                                <td>{{$offer['net_click']}}</td>
                                                <td>{{ $offer['net_cr'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.col-lg-4 (nested) -->

                            <!-- /.col-lg-8 (nested) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.panel-body -->
                </div>            <!-- /.panel -->

                <!-- /.panel -->
            </div>
        </div>
    @endif


    <div class="row">
        <!-- /.col-lg-8 -->
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Recent Lead
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        @foreach ($userRecent as $ip => $offer)
                            <a class="list-group-item" href="#">
                                <b>{{$offer['username']}} </b> lead offer <b>{{$offer['offer_name']}}</b> with IP <b>{{$ip}}</b>
                                <span class="pull-right text-muted small">
                                    <em>{{$offer['time']}}</em>
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <!-- /.list-group -->
                    <a class="btn btn-default btn-block" href="{{url('admin/offers')}}">View All Offers</a>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-4 -->
    </div>

@endsection