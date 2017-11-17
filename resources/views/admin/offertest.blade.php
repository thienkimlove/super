@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Offer Test</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Add</h2>

            <form action="{{url('admin/offertest')}}" method="POST">


                <div class="form-group">
                    {!! Form::label('url', 'URL') !!}
                    {!! Form::text('url', null, ['class' => 'form-control']) !!}
                    <input type="hidden" value="{{csrf_token()}}">
                </div>

                <div class="form-group">
                    {!! Form::label('country', 'Country') !!}
                    {!! Form::text('country', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('device', 'Choose Device') !!}
                    {!! Form::select('device', ['ios' => 'IOS', 'android' => 'Android'], null, ['class' => 'form-control']) !!}
                </div>



                <div class="form-group">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary form-control']) !!}
                </div>

            </form>

            @include('admin.list')

        </div>
    </div>
@endsection