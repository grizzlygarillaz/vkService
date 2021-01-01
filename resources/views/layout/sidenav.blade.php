@extends('layout.page')
@section('sidenav')
<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="/">
                    VkService
                </a>
            </li>
            <li><a href="/">Главная страница</a></li>
            <li><a href="/post">Отправить пост</a></li>
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-sm-start justify-content-between align-items-center">
                        <a href="#menu-toggle" id="menu-toggle">
                            <div class="wrapper-menu open">
                                <div class="line-menu half start"></div>
                                <div class="line-menu"></div>
                                <div class="line-menu half end"></div>
                            </div>
                        </a>
                        <p class="page" style="font-weight: 500; margin-left: 45%; font-family: 'Roboto'; color: #353b49">{{ $page }}</p>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $('.wrapper-menu').toggleClass('open');
    });


</script>
@endsection
