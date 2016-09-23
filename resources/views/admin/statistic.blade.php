<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Thống kê</h1>
    </div>

    <div class="col-lg-4">
        <h2>Thống kê theo group</h2>
        {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/group')]) !!}

        <div class="form-group">
            {!! Form::label('content_id', 'Group') !!}
            {!! Form::select('content_id', $globalGroups, null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('start', 'Ngày bắt đầu') !!}
            {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-group-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('end', 'Ngày kết thúc') !!}
            {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-group-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
        </div>

        {!! Form::close() !!}

    </div>

    <div class="col-lg-4">
        <h2>Thống kê theo User</h2>
        {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/user')]) !!}

        <div class="form-group">
            {!! Form::label('content_id', 'Nhập tên user') !!}
            {!! Form::text('content_id', null, ['class' => 'form-control', 'id' => 'user_suggest']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('start', 'Ngày bắt đầu') !!}
            {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-user-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('end', 'Ngày kết thúc') !!}
            {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-user-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
        </div>

        {!! Form::close() !!}

    </div>

    <div class="col-lg-4">
        <h2>Thống kê theo offer</h2>
        {!! Form::open(['method' => 'GET', 'url' => url('admin/statistic/offer')]) !!}

        <div class="form-group">
            {!! Form::label('content_id', 'Offer') !!}
            {!! Form::select('content_id', $globalOffers, null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('start', 'Ngày bắt đầu') !!}
            {!! Form::text('start', null, ['class' => 'form-control', 'id' => 'start-offer-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('end', 'Ngày kết thúc') !!}
            {!! Form::text('end', null, ['class' => 'form-control', 'id' => 'end-offer-date']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Thống kê', ['class' => 'btn btn-primary form-control']) !!}
        </div>

        {!! Form::close() !!}

    </div>

</div>

