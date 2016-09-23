@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Groups</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (!empty($group))
            <h2>Edit</h2>
            {!! Form::model($group, ['method' => 'PATCH', 'route' => ['groups.update', $group->id], 'files' => true]) !!}
            @else
                <h2>Add</h2>
                {!! Form::model($group = new App\Group, ['route' => ['groups.store'], 'files' => true]) !!}
            @endif

            <div class="form-group">
                {!! Form::label('name', 'Group Name') !!}
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