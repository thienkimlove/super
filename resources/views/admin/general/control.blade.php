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
    </div>


    <div class="row">
        <div class="col-lg-8">
            <!-- /.panel -->
            <div class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Danh sách offer chạy ngày hôm nay
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Clicks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($offers as $offer)
                                        <tr>
                                            <td>{{$offer->name}}</td>
                                            <td>{{$offer->clicks->count()}}</td>
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
        <!-- /.col-lg-8 -->
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Recent Lead
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        @foreach ($recentOffers as $offer)
                            <a class="list-group-item" href="#">
                                <i class="fa fa-comment fa-fw"></i> {{$offer->name}}
                                <span class="pull-right text-muted small">
                                    <em>{{$offer->updated_at->toDayDateTimeString()}}</em>
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