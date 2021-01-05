@extends('layout.page')
@section('sidenav')
    <div id="wrapper" class="toggled">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="/">
                        VkService
                    </a>
                </li>
                <li><a href="/">Главная страница</a></li>
                <li><a href="/projects">Проекты</a></li>
                <li class="accordion accordion-flush" style="background-color: rgb(58,56,73)" id="accordionPost">
                    <div class="accordion-item">
                        <a class="accordion-button collapsed" data-bs-toggle="collapse"
                           data-bs-target="#flush-collapseOne" aria-expanded="false"
                           aria-controls="flush-collapseOne">
                            Посты
                        </a>
                        <div id="flush-collapseOne" style="background-color: rgb(51, 49, 64)"
                             class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                             data-bs-parent="#accordionPost">
                            <a href="/post" id="collapse1" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Отправить пост</a>
                            <a href="/post" id="collapse2" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Отложенные посты</a>
                            <a href="/post/tags" id="collapse3" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Настройка тегов</a>
                        </div>
                    </div>
                </li>
                <li><a href="/promo">Промо-акции</a></li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div
                            class="header mb-1 d-flex py-1 justify-content-sm-start justify-content-between align-items-center">
                            <a href="#menu-toggle" id="menu-toggle">
                                <div class="wrapper-menu">
                                    <div class="line-menu half start"></div>
                                    <div class="line-menu"></div>
                                    <div class="line-menu half end"></div>
                                </div>
                            </a>
                            <p class="page">{{ $page }}</p>
                        </div>
                        <div class="content-background">
                        @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <script>
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $('.wrapper-menu').toggleClass('open');
        });

        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
@endsection
