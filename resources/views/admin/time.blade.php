<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <title>Admin</title>

    <!-- Custom Fonts -->
    <link href="{{ url('/css/admin/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/admin/select2.min.css')}}" rel="stylesheet" />
    <link href="{{ url('js/admin/datetimepicker/build/jquery.datetimepicker.min.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

</head>

<body>

<div id="wrapper">



    <div id="page-wrapper">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i>Site Recent Lead
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        @foreach ($siteRecentLead as $recent)
                            <a class="list-group-item" href="#">
                                <b>{{$recent->username}} </b> lead offer <b>{{$recent->name}}</b> with IP <b>{{$recent->ip}}</b> - ID=<b>{{$recent->id}} | Time Click: {{$recent->click_at}} || postbackId= {{$recent->postback_id}} | Network={{$recent->network_name}}</b>
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
    </div>


</div>
<script>
    var Config = {};
    window.baseUrl = '{{url('/')}}';
</script>

<script src="{{url('/js/admin/admin.js')}}"></script>
<script src="{{url('/js/admin/ckeditor/ckeditor.js')}}"></script>
<script src="{{url('/js/admin/select2.min.js')}}"></script>
<script src="{{url('js/admin/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf_token]').attr('content') }
    });
    var baseUrl = "{{url('/')}}";

</script>
@yield('footer')
</body>
</html>
