@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Welcome Administrator.
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-comments fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$content['today']}}</div>
                            <div>Money Today</div>
                        </div>
                    </div>
                </div>
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
                            <div class="huge">{{$content['month']}}</div>
                            <div>Money This Month!</div>
                        </div>
                    </div>
                </div>
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
                            <div class="huge">{{$content['total']}}</div>
                            <div>Total Money!</div>
                        </div>
                    </div>
                </div>
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
                                        <th>Leads</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($todayOffers as $offer)
                                        <tr>
                                            <td>{{$offer['offer_name']}}</td>
                                            <td>{{$offer['net_lead']}}</td>
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
                                            <th>Leads</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($yesterdayOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['net_lead']}}</td>
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
                                            <th>Leads</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($weekOffers as $offer)
                                            <tr>
                                                <td>{{$offer['offer_name']}}</td>
                                                <td>{{$offer['net_lead']}}</td>
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

    @if ($userRecent)
    <div class="row">
        <!-- /.col-lg-8 -->
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Your Recent Lead
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        @foreach ($userRecent as $recent)
                            <a class="list-group-item" href="#">
                                <b>You</b> lead offer <b>{{$recent->name}}</b> with IP <b>{{$recent->ip}}</b>
                                <span class="pull-right text-muted small">
                                    <em>{{$recent->created_at}}</em>
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
    @endif
    <div class="row" id="site-recent-lead">

    </div>

@endsection

@section('footer')
<script>
    $(document).ready(function(){
        setInterval(function(){
            $.getJSON(baseUrl + '/admin/recent-lead', function(response){
                $('#site-recent-lead').html(response.html);
            });
        }, 3000);
    });
</script>
@endsection