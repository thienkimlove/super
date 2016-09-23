<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin</title>

    <!-- Custom Fonts -->
    <link href="{{ url('/css/admin/admin.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/css/admin/select2.min.css')}}" rel="stylesheet" />
    <link href="{{ url('js/admin/datetimepicker/build/jquery.datetimepicker.min.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

</head>

<body>

<div id="wrapper">
   
    @include('admin.nav')

    <div id="page-wrapper">
     @include('flash::message')
        @if (auth('backend')->user()->permission_id == 1)
            @include('admin.statistic')
        @endif
        @yield('content')
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

@yield('footer')
</body>
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
            format:'Y-m-d'
        });

        var availableTags = '{{ implode("##", $globalUsers) }}';

        availableTags = availableTags.split('##');

        jQuery( "#user_suggest" ).autocomplete({
            source: availableTags
        });

    });
</script>

</html>
