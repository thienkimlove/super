<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <!-- /.navbar-header -->
     <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand">Admin</a>
    </div>

    <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown-user -->
        <!-- /.dropdown -->

          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>           
            <!-- /.dropdown-tasks -->
        </li>
        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>           
            <!-- /.dropdown-alerts -->
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="{{url('admin/logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
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
                                <a href="{{url('admin/thongke')}}">Thống kê</a>
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


                            <li>
                                <a href="{{url('admin/offers?auto=1')}}">Danh sách offer tự động</a>
                            </li>

                            <li>
                                <a href="{{url('admin/offers?auto=1&inactive=1')}}">Danh sách offer đã dừng</a>
                            </li>


                            <li>
                                <a href="{{url('admin/groups')}}">Danh sách Group</a>
                            </li>

                            <li>
                                <a href="{{url('admin/groups/create')}}">Thêm Group</a>
                            </li>

                            <li>
                                <a href="{{url('admin/networks')}}">Danh sách network</a>
                            </li>

                            <li>
                                <a href="{{url('admin/networks/create')}}">Thêm network</a>
                            </li>

                            <li>
                                <a href="{{url('admin/cron')}}">Run Cron to Update Offers</a>
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
                            <a href="{{url('admin/offers?auto=1')}}">Danh sách offer tự động</a>
                        </li>
                        @if (auth('backend')->user()->permission_id == 1)
                        <li>
                            <a href="{{url('admin/offers/create')}}">Add</a>
                        </li>
                        @endif
                    </ul>
                </li>

            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
