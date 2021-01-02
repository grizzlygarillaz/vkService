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
                <li class="accordion accordion-flush" style="background-color: rgb(58,56,73)" id="accordionPost">
                    <div class="accordion-item">
                        <a class="accordion-button collapsed" data-bs-toggle="collapse"
                           data-bs-target="#flush-collapseOne" aria-expanded="false"
                           aria-controls="flush-collapseOne">
                            Управление постами
                        </a>
                        <div id="flush-collapseOne" style="background-color: rgb(51, 49, 64)"
                             class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                             data-bs-parent="#accordionPost">
                            <a href="/post" id="collapse2" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Отправить пост</a>
                            <a href="/post/tags" id="collapse3" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Настройка тегов</a>
                        </div>
                    </div>
                </li>
                <li class="accordion accordion-flush" style="background-color: rgb(58,56,73)" id="accordionPromo">
                    <div class="accordion-item">
                        <a class="accordion-button collapsed" data-bs-toggle="collapse"
                           data-bs-target="#flush-collapseTwo" aria-expanded="false"
                           aria-controls="flush-collapseTwo">
                            Управление промо-акциями
                        </a>
                        <div id="flush-collapseTwo" style="background-color: rgb(51, 49, 64)"
                             class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                             data-bs-parent="#accordionPromo">
                            <a href="/promo/projects" id="collapse1" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Акции проектов</a>
                            <a href="/promo" id="collapse2" class="collapse show ps-3" aria-labelledby="headingOne"
                               data-parent="#accordionExample">Список акций</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="header d-flex justify-content-sm-start justify-content-between align-items-center">
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
                            <div class="content" style="">
                                @yield('content')
                            </div>
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
    </script>
@endsection
