@extends('admin')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Network</h1>
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
                                <th>Name</th>
                                <th>PostBack Link</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($networks as $network)
                                <tr>
                                    <td>{{$network->id}}</td>
                                    <td>{{$network->name}}</td>
                                    <td>{{url('postback?network_id='. $network->id)}}</td>

                                    <td>
                                        <button id-attr="{{$network->id}}" class="btn btn-primary btn-sm edit-content" type="button">Edit</button>&nbsp;
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['networks.destroy',
                                        $network->id]]) !!}
                                        <button type="submit" class="btn btn-danger btn-mini">Delete</button>
                                        {!! Form::close() !!}

                                        @if ($network->cron)

                                        <button id-attr="{{$network->id}}" class="btn btn-primary btn-sm cron-content" type="button">Cron</button>&nbsp;
                                         @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">{!!$networks->render()!!}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <button class="btn btn-primary add-content" type="button">Add</button>
                        </div>
                    </div>


                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

    </div>
@endsection
@section('footer')
    <script>
        $(function(){
            $('.add-content').click(function(){
                window.location.href = window.baseUrl + '/admin/networks/create';
            });
            $('.edit-content').click(function(){
                window.location.href = window.baseUrl + '/admin/networks/' + $(this).attr('id-attr') + '/edit';
            });
            $('.cron-content').click(function(){
                window.location.href = window.baseUrl + '/admin/cron?network_id=' + $(this).attr('id-attr');
            });
        });
    </script>
@endsection