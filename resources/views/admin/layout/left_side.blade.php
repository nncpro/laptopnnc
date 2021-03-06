<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="@if (Auth::guard('admin')->check()){{ asset(Auth::guard('admin')->user()->avatar) }} @else {{ asset('images/default-user.jpg') }} @endif" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>
                    @if (Auth::guard('admin')->check())
                        {{ Auth::guard('admin')->user()->name }}
                    @else
                        Test Admin Name
                    @endif
                </p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        {{-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form> --}}
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MENU CHÍNH</li>
            <li class="">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Bảng điều khiển</span>
                    <!-- <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span> -->
                </a>
            </li>
            @if (Auth::guard('admin')->user()->role == 1)


                <li class="treeview">
                    <a href="{{ route('admins.index') }}">
                        <i class="fa fa-user"></i>
                        <span>Quản lý Admin</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('admins.index') }}"><i class="fa fa-circle-o"></i>Danh sách Admin</a></li>
                        <li><a href="{{ route('admins.create') }}"><i class="fa fa-circle-o"></i>Thêm Admin</a></li>
                    </ul>
                </li>
            @endif

            <li class="">
                <a href="{{ route('categories.index') }}">
                    <i class="fa fa-sitemap"></i>
                    <span>Quản lý danh mục</span>
                    <!-- <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span> -->
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-laptop"></i> <span>Quản lý sản phẩm</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('product.index') }}"><i class="fa fa-circle-o"></i> Danh sách sản phẩm</a>
                    </li>
                    <li><a href="{{ route('product.create') }}"><i class="fa fa-circle-o"></i> Thêm sản phẩm mới</a>
                    </li>

                    {{-- Thông tin CPU-GPU --}}
                    <li class="treeview">
                        <a style="font-style: italic; font-weight: bold; background-color: #fff" href="#">
                            <i class="fa fa-flash"></i>
                            <span> Thông tin CPU-GPU</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span></a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('cpu.index') }}"><i class="fa fa-circle-o"></i> <span>Thông tin
                                        CPU</span></a></li>
                            <li><a href="{{ route('gpu.index') }}"><i class="fa fa-circle-o"></i> <span>Thông tin
                                        GPU</span></a></li>
                        </ul>
                    </li>

                    {{-- End Thông tin CPU-GPU --}}
                    {{-- Quản lý ảnh --}}
                    {{-- <li><a style="font-style: italic; font-weight: bold; background-color: #fff;" href="{{route('products.manage_image')}}"><i class="fa fa-file-image-o"></i>Hình ảnh sản phẩm</a></li> --}}
                    {{-- Quản lý kho --}}
                    {{-- <li><a style="font-style: italic; font-weight: bold; background-color: #fff;" href="{{ route('products.manage_stock') }}"><i class="fa fa-dollar"></i> Quản lý kho</a></li> --}}
                    {{-- Quản lý bảo hành --}}
                    <li><a style="font-style: italic; font-weight: bold; background-color: #fff;"
                            href="{{ route('warranty.index') }}"><i class="fa fa-wrench"></i>Sản phẩm bảo hành</a>
                    </li>
                </ul>

            </li>
            {{-- Quản lý khuyến mại --}}
            <li class=""><a href="{{ route('promotion.index') }}"><i
                        class="fa fa-gift"></i><span>Quản lý khuyến mại</span></a></li>
            {{-- Quản lý hóa đơn --}}
            <li class=""><a href="{{ route('order.index') }}"><i
                        class="fa fa-sticky-note-o"></i><span>Quản lý đơn hàng</span></a></li>
            {{-- Quản lý bài viết --}}
            <li class=""><a href="{{ route('post.index') }}"><i
                        class="fa fa-pencil-square-o"></i><span>Quản lý bài viết</span></a></li>
            <li class=""><a href="{{ route('customer.index') }}"><i
                        class="fa fa-user-secret"></i><span>Quản lý khách hàng</span></a></li>
            <li class=""><a href="{{ route('user.index') }}"><i class="fa fa-users"></i><span>Quản lý
                        User</span></a></li>
            <li class=""><a href="{{ route('slider.index') }}"><i class="fa fa-picture-o"></i><span>Quản lý
                        Slider - Banner</span></a></li>


    </section>
    <!-- /.sidebar -->
</aside>
