@extends('admin')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Offers</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="input-group custom-search-form">
                        {!! Form::open(['method' => 'GET', 'url' => url('admin/offers') ]) !!}
                        <span class="input-group-btn">
                            <input type="text" value="{{$searchOffer}}" name="q" class="form-control" placeholder="Search offer..">

                            <button class="btn btn-default" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>

                        {!! Form::close() !!}
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Offer Name</th>
                                <th>Cap</th>
                                <th>Link</th>
                                <th>Price</th>
                                <th>Geo</th>
                                <th>OS</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{$offer['offer_id']}}</td>
                                    <td>{{$offer['offer_name']}}</td>
                                    <td>?</td>
                                    <td>{{$offer['preview_link']}}</td>
                                    <td>{{$offer['offer_commission']}}</td>
                                    <td>{{$offer['offer_countries']}}</td>
                                    <td>{{$offer['offer_targeting']}}</td>
                                    <td>{{$offer['offer_status']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
            $('.edit-post').click(function(){
                window.location.href = window.baseUrl + '/admin/offers/' + $(this).attr('id-attr');
            });
        });
    </script>
@endsection