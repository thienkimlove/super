@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Offers</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (!empty($offer))
            <h2>Edit</h2>
            {!! Form::model($offer, ['method' => 'PATCH', 'route' => ['offers.update', $offer->id], 'files' => true])
             !!}
            @else
                <h2>Add</h2>
                {!! Form::model($offer = new App\Offer, ['route' => ['offers.store'], 'files' => true]) !!}
            @endif

            <div class="form-group">
                {!! Form::label('name', 'Offer Name') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>

                <div class="form-group">
                    {!! Form::label('net_offer_id', 'Network Offer Id') !!}
                    {!! Form::text('net_offer_id', null, ['class' => 'form-control']) !!}
                </div>

            <div class="form-group">
                {!! Form::label('redirect_link', 'Redirect Link with "#subId" at end of Link') !!}
                {!! Form::text('redirect_link', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('click_rate', 'Price Per Click') !!}
                {!! Form::text('click_rate', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('geo_locations', 'Allow Geo Locations') !!}
                {!! Form::text('geo_locations', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('allow_devices', 'Choose Allow Devices') !!}
                {!! Form::select('allow_devices', $devices, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('network_id', 'Choose network') !!}
                {!! Form::select('network_id', $networks, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('image', 'Image Preview') !!}
                {!! Form::text('image', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('allow_multi_lead', 'Allow Multi Lead') !!}
                {!! Form::checkbox('allow_multi_lead', null, null) !!}
            </div>

            <div class="form-group">
                {!! Form::label('check_click_in_network', 'Check Click In Network') !!}
                {!! Form::checkbox('check_click_in_network', null, null) !!}
            </div>

            <div class="form-group">
                {!! Form::label('number_when_click', 'Number of Virtual Clicks When Have Click') !!}
                {!! Form::number('number_when_click', null, ['class' => 'form-control']) !!}
            </div>


            <div class="form-group">
                {!! Form::label('number_when_lead', 'Number of Virtual Clicks When Have Lead') !!}
                {!! Form::number('number_when_lead', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('status', 'Active') !!}
                {!! Form::checkbox('status', null, null) !!}
            </div>


            <div class="form-group">
                {!! Form::submit('Save', ['class' => 'btn btn-primary form-control']) !!}
            </div>

            {!! Form::close() !!}

            @include('admin.list')

        </div>
    </div>
@endsection