<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-brand">Admin</a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="{{url('admin/logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">

                <li>
                    <a href="{{url('admin')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                </li>


                @if (auth('backend')->user()->permission_id == 1)

                    <li>
                        <a><i class="fa fa-files-o fa-fw"></i>Admin<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">

                            <li>
                                <a href="{{url('admin/control')}}">Bảng điều khiển admin</a>
                            </li>

                            <li>
                                <a href="{{url('admin/users')}}">Danh sách thành viên</a>
                            </li>

                            <li>
                                <a href="{{url('admin/users?filter=1')}}">Thành viên bị cấm</a>
                            </li>

                            <li>
                                <a href="{{url('admin/users/create')}}">Thêm thành viên</a>
                            </li>

                            <li>
                                <a href="{{url('admin/offers/create')}}">Thêm offer</a>
                            </li>

                            <li>
                                <a href="{{url('admin/offers')}}">Danh sách offer</a>
                            </li>

                        </ul>
                    </li>

                @endif

                <li>
                    <a><i class="fa fa-files-o fa-fw"></i>Offers<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">

                        <li>
                            <a href="{{url('admin/offers')}}">List</a>
                        </li>
                        <li>
                            <a href="{{url('admin/offers/create')}}">Add</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>