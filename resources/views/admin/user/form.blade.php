@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Users</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (!empty($user))
            <h2>Edit</h2>
            {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id], 'files' => true]) !!}
            @else
                <h2>Add</h2>
                {!! Form::model($user = new App\User, ['route' => ['users.store'], 'files' => true]) !!}
            @endif

            <div class="form-group">
                {!! Form::label('email', 'Email') !!}
                {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('username', 'Username') !!}
                {!! Form::text('username', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('contact', 'Contact') !!}
                {!! Form::textarea('contact', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('permission_id', 'Level') !!}
                {!! Form::select('permission_id', $permissions, null, ['class' => 'form-control']) !!}
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