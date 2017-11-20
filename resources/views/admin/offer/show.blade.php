@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Offers</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">


            <div class="form-group">
                {!! Form::label('name', 'Offer Name') !!}
                <span>{{$offer->name}}</span>
            </div>


            <div class="form-group">
                {!! Form::label('test_click', 'Offer Test Last Url') !!}
                <span>{{$offer->test_click}}</span>
            </div>


        </div>
    </div>
@endsection