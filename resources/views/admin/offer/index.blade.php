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
                        </span>
                        <span class="input-group-btn">
                             <input type="text" value="{{$searchCountry}}" name="country" class="form-control"
                                    placeholder="Search country..">

                        </span>

                        <span class="input-group-btn">
                             <input type="text" value="{{$searchUid}}" name="uid" class="form-control"
                                    placeholder="Search Id..">

                        </span>

                        <span class="input-group-btn">
                           {!! Form::select('device', $devices, ($searchDevice) ? $searchDevice : null, ['class' => 'form-control']) !!}
                        </span>
                        @if (auth('backend')->user()->permission_id == 1)

                        <span class="input-group-btn">
                              {!! Form::select('network', $networks, ($searchNetwork) ? $searchNetwork : null, ['class' => 'form-control']) !!}

                        </span>
                        @endif
                        <span class="input-group-btn">
                             @if ($searchAuto == 1)
                             <input type="hidden" name="auto" value="{{$searchAuto}}" />
                             @endif

                             @if ($searchInactive)
                             <input type="hidden" name="inactive" value="{{$searchInactive}}" />
                             @endif
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
                                @if (auth('backend')->user()->permission_id == 1)
                                    <th>Network OfferID</th>
                                @endif
                                <th>Name</th>
                                <th>Image</th>
                                <th>Price Per Click</th>
                                <th>Geo Locations</th>
                                <th>Allow Devices</th>
                                <th>Link To Lead</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                @if (auth('backend')->user()->permission_id == 1)
                                    <th>True Link</th>
                                    <th>Allow Multi Lead</th>
                                    <th>Check Click In Network</th>
                                    <th>Virtual Clicks</th>
                                    <th>Network</th>
                                    <th>Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{$offer->id}}</td>
                                    @if (auth('backend')->user()->permission_id == 1)
                                        <td>{{$offer->net_offer_id}}</td>
                                    @endif
                                    <td style="width:10%;">{{$offer->name}}</td>
                                    <td>
                                        @if ($offer->image)
                                            <img src="{{$offer->image}}" height="60" width="60" />
                                        @endif
                                    </td>
                                    <td>{{$offer->click_rate}}</td>
                                    <td style="width:10%;">{{$offer->geo_locations}}</td>
                                    <td>{{config('devices')[$offer->allow_devices]}}</td>
                                    <td>{{url('camp?offer_id='.$offer->id.'&user_id='.auth('backend')->user()->id)}}</td>
                                    <td>{{($offer->status) ? 'Active' : "Inactive"}}</td>
                                    <td>{{$offer->created_at->format('Y-m-d H:i:s')}}</td>
                                    @if (auth('backend')->user()->permission_id == 1)
                                        <td>{{$offer->redirect_link}}</td>
                                        <td>{{($offer->allow_multi_lead) ? 'Yes' : 'No'}}</td>
                                        <td>{{($offer->check_click_in_network) ? 'Yes' : 'No'}}</td>
                                        <td>
                                            <span>Number clicks when have click:</span> {{$offer->number_when_click}} <br/>
                                            <span>Number clicks when have lead:</span> {{$offer->number_when_lead}} <br/>
                                        </td>
                                        <td>{{($offer->network) ? $offer->network->name : 'None'}}</td>
                                        <td>
                                            <button id-attr="{{$offer->id}}" class="btn btn-primary btn-sm edit-content" type="button">Edit</button>&nbsp;
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['offers.destroy',
                                            $offer->id]]) !!}
                                            <button type="submit" class="btn btn-danger btn-mini">Delete</button>
                                            {!! Form::close() !!}

                                            <button lead-attr="{{$offer->id}}" class="btn btn-primary btn-sm lead-content" type="button">Xóa IP đã Lead</button>&nbsp;
                                            <br />

                                            @if (!$offer->test_link)

                                                <div class="test-content">
                                                    <button test-attr="{{$offer->id}}" class="btn btn-primary btn-sm" type="button">Test Offer</button>&nbsp;
                                                </div>
                                             @else
                                              <b>{{$offer->test_link}}</b>
                                             @endif
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

                    @if (auth('backend')->user()->permission_id == 1)
                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary add-content" type="button">Add</button>
                            </div>
                        </div>
                    @endif

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
            $('.test-content').click(function(){
                var element = $(this);
                element.html('Loading...');
                window.location.href = window.baseUrl + '/admin/offertest/' + $(this).attr('test-attr') ;
                $.get(baseUrl + '/admin/offertest/' + $(this).attr('test-attr'), function(res){
                    element.html(res.data);
                });
            });
        });
    </script>
@endsection