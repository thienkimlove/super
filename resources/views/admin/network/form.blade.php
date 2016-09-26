@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Networks</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (!empty($network))
            <h2>Edit</h2>
            {!! Form::model($network, ['method' => 'PATCH', 'route' => ['networks.update', $network->id], 'files' => true]) !!}
            @else
                <h2>Add</h2>
                {!! Form::model($network = new App\Network, ['route' => ['networks.store'], 'files' => true]) !!}
            @endif

            <div class="form-group">
                {!! Form::label('name', 'Network Name') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>


            <div class="form-group">
                {!! Form::submit('Save', ['class' => 'btn btn-primary form-control']) !!}
            </div>

            {!! Form::close() !!}

            @include('admin.list')

        </div>
    </div>
@endsection