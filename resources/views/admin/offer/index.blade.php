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
                        {!! Form::open(['method' => 'GET', 'route' =>  ['offers.index'] ]) !!}
                        <span class="input-group-btn">
                            <input type="text" value="{{$searchOffer}}" name="q" class="form-control"
                                   placeholder="Search offer..">

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
                                <th>#</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Price Per Click</th>
                                <th>Geo Locations</th>
                                <th>Allow Devices</th>
                                <th>Link To Lead</th>
                                <th>Status</th>
                                @if (auth('backend')->user()->permission_id == 1)
                                    <th>True Link</th>
                                    <th>Network</th>
                                    <th>Network OfferID</th>
                                    <th>Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{$offer->id}}</td>
                                    <td>{{$offer->name}}</td>
                                    <td>
                                        @if ($offer->image)
                                            <img src="{{$offer->image}}" />
                                        @endif
                                    </td>
                                    <td>{{$offer->click_rate}}</td>
                                    <td>{{$offer->geo_locations}}</td>
                                    <td>{{config('devices')[$offer->allow_devices]}}</td>
                                    <td>{{url('camp?offer_id='.$offer->id.'&user_id='.auth('backend')->user()->id)}}</td>
                                    <td>{{($offer->status) ? 'Active' : "Inactive"}}</td>
                                    @if (auth('backend')->user()->permission_id == 1)
                                        <td>{{$offer->redirect_link}}</td>
                                        <td>{{($offer->network) ? $offer->network->name : 'None'}}</td>
                                        <td>{{$offer->net_offer_id}}</td>
                                        <td>
                                            <button id-attr="{{$offer->id}}" class="btn btn-primary btn-sm edit-content" type="button">Edit</button>&nbsp;
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['offers.destroy',
                                            $offer->id]]) !!}
                                            <button type="submit" class="btn btn-danger btn-mini">Delete</button>
                                            {!! Form::close() !!}

                                            <button lead-attr="{{$offer->id}}" class="btn btn-primary btn-sm lead-content" type="button">Xóa IP đã Lead</button>&nbsp;
                                        </td>
                                     @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">{!!$offers->render()!!}</div>
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
                window.location.href = window.baseUrl + '/admin/offers/create';
            });
            $('.edit-content').click(function(){
                window.location.href = window.baseUrl + '/admin/offers/' + $(this).attr('id-attr') + '/edit';
            });

            $('.lead-content').click(function(){
                window.location.href = window.baseUrl + '/admin/clearlead/?offer_id=' + $(this).attr('lead-attr') ;
            });
        });
    </script>
@endsection