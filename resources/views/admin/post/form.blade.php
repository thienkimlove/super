@extends('admin')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Posts</h1>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (!empty($post))
            <h2>Edit</h2>
            {!! Form::model($post, [
                'method' => 'PATCH',
                'route' => ['posts.update', $post->id],
                'files' => true
             ]) !!}
            @else
                <h2>Add</h2>
                {!! Form::model($post = new App\Post, ['route' => ['posts.store'], 'files' => true]) !!}
            @endif

            <div class="form-group">
                {!! Form::label('title', 'Title') !!}
                {!! Form::text('title', null, ['class' => 'form-control']) !!}
            </div>


            <div class="form-group">
                {!! Form::label('category_id', 'Category') !!}
                {!! Form::select('category_id', $categories, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('desc', 'Short Description') !!}
                {!! Form::textarea('desc', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('content', 'Content') !!}
                {!! Form::textarea('content', null, ['class' => 'form-control ckeditor']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('image', 'Image') !!}
                @if ($post->image)
                    <img src="{{url('img/cache/small/' . $post->image)}}" />
                    <hr>
                @endif
                {!! Form::file('image', null, ['class' => 'form-control']) !!}
            </div>




            <div class="form-group">
                {!! Form::label('status', 'Publish') !!}
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
