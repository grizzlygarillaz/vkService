@extends('layout.page')
@section('sidenav')
    <div id="wrapper" class="toggled">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">

                @if(\Illuminate\Support\Facades\Auth::check())
                    <h4 class="mx-4 my-2 text-light">{{ \Illuminate\Support\Facades\Auth::user()->name }}</h4>
                    <hr class="p-0 m-0" style="color: #9eacdd">
                @endif
                <li><a href="/">Главная страница</a></li>
                <li><a href="/projects">Проекты</a></li>
                <li><a href="/content_plan">Контент-план</a></li>
                {{--                <li><a href="/promo">Промо-акции</a></li>--}}
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                    <li><a href="/settings/tags">Настройка тегов</a></li>
                    <li><a href="/import">Импортировать проекты</a></li>
                    <li><a href="/employees">Список сотрудников</a></li>
                    <li><a href="/register/employee">Добавить сотрудника</a></li>
                @endif
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'manager')
                    <li><a href="/settings/tags">Список тегов</a></li>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check())
                    <li>
                        <hr class="p-0 m-0 mb-5" style="color: #9eacdd">
                        <form action="/logout" class="d-flex" method="post">
                            @csrf
                            <button type="submit" class="logout btn w-100 text-start ps-4">Выйти</button>
                        </form>
                    </li>
                @endif
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        @if($errors->any())
                            <div id="system_error" class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div
                            class="header mb-1 d-flex py-1 justify-content-sm-start justify-content-between align-items-center">
                            <a href="#menu-toggle" id="menu-toggle">
                                <div class="wrapper-menu" id="wrapper-menu">
                                    <div class="line-menu half start"></div>
                                    <div class="line-menu"></div>
                                    <div class="line-menu half end"></div>
                                </div>
                            </a>
                            <p class="page">{{ $page }}</p>
                        </div>
                        <div class="content-background">
                            <a id="button-to-top" class="material-icons" style="font-size: xxx-large">
                                keyboard_arrow_up
                            </a>
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <script>
        let navbar = localStorage.getItem('navbar');

        if (navbar == 'open') {
            $('*').addClass('no-transition')
            $("#wrapper").removeClass("toggled")
            $('.wrapper-menu').addClass('open')
        }

        $(document).ready(function () {
            $('#system_error').click(function () {
                $(this).hide('slow', function () {
                    $(this).remove();
                });
            })
            $('*').removeClass('no-transition')
            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled")
                $('.wrapper-menu').toggleClass('open')
                if ($('.wrapper-menu').hasClass('open')) {
                    localStorage.setItem("navbar", 'open')
                } else {
                    localStorage.setItem("navbar", 'close')
                }
            })

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
        });
    </script>
@endsection
