@extends('admin')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Thống kê</h1>
        </div>

        <div class="col-lg-4">
            <h2>Thống kê theo group</h2>
            {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/group')]) !!}

            <div class="form-group">
                {!! Form::label('content_id', 'Group') !!}
                {!! Form::select('content_id', $globalGroups, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('start', 'Ngày bắt đầu') !!}
                {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-group-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('end', 'Ngày kết thúc') !!}
                {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-group-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
            </div>

            {!! Form::close() !!}

        </div>

        <div class="col-lg-4">
            <h2>Thống kê theo User</h2>
            {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/user')]) !!}

            <div class="form-group">
                {!! Form::label('content_id', 'Nhập tên user') !!}
                {!! Form::text('content_id', null, ['class' => 'form-control', 'id' => 'user_suggest']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('start', 'Ngày bắt đầu') !!}
                {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-user-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('end', 'Ngày kết thúc') !!}
                {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-user-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
            </div>

            {!! Form::close() !!}

        </div>

        <div class="col-lg-4">
            <h2>Thống kê theo offer</h2>
            {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/offer')]) !!}

            <div class="form-group">
                {!! Form::label('content_id', 'Offer') !!}
                {!! Form::select('content_id', $globalOffers, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('start', 'Ngày bắt đầu') !!}
                {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-offer-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('end', 'Ngày kết thúc') !!}
                {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-offer-date']) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
            </div>

            {!! Form::close() !!}

        </div>

    </div>

    @if (isset($title) && isset($clicks) && isset($totalClicks) && isset($totalMoney))

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
                                    <td>{{ ($click->offer_allow_devices) ? config('devices')[$click->offer_allow_devices] : '' }}</td>
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
                        <div class="col-sm-4">Total Leads: <b>{{$totalClicks}}</b></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">Total Money : <b> {{$totalMoney}}</b></div>
                    </div>


                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

    </div>

    @endif
@endsection

@section('footer')
    <script>
        $(document).ready(function(){
            jQuery.datetimepicker.setLocale('vi');

            jQuery('#start-group-date, #end-group-date, #start-user-date, #end-user-date, #start-offer-date, #end-offer-date').datetimepicker({
                i18n:{
                    vi:{
                        months:[
                            'Thang 1','Thang 2','Thang 3','Thang 4',
                            'Thang 5','Thang 6','Thang 7','Thang 8',
                            'Thang 9','Thang 10','Thang 11','Thang 12',
                        ],
                        dayOfWeek:[
                            "Chu Nhat", "Thu 2", "Thu 3", "Thu 4",
                            "Thu 5", "Thu 6", "Thu 7",
                        ]
                    }
                },
                timepicker:false,
                minDate:'1970-01-02',
                format:'Y-m-d'
            });

            var availableTags = '{{ implode("##", $globalUsers) }}';

            availableTags = availableTags.split('##');

            jQuery( "#user_suggest" ).autocomplete({
                source: availableTags
            });

        });
    </script>
@endsection