@extends('layout.sidenav')
@section('content')
    <div class="content text-start" style="">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            @foreach($objects as $key => $object)
                <input type="radio" class="btn-check setting-page" name="setting-page" id="{{ $key }}"
                       autocomplete="off">
                <label class="btn btn-outline-primary" for="{{ $key }}">{{ $object['name'] }}</label>
            @endforeach
        </div>
        <hr class="text-dark m-2">

        @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
            <div class="d-flex">
                <button class="update-tags btn btn-outline-success">Обновить данные</button>
                <div class="alert ms-3 mb-0 p-2 alert-warning" role="alert">
                    Не забудьте <strong>сохранить</strong> изменения, нажав на <strong>"Обновить данные"</strong>
                </div>
            </div>
            <hr class="text-dark m-2">
        @endif

        <div class="d-flex justify-content-center">
            <div class="setting-content align-self-center w-100 w-lg-75">
            </div>
        </div>
    </div>

    <script>
        @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
        $('.update-tags').click(function () {
            let data = {}
            $('.setting-content :input').each(function () {
                if ($(this).attr('type') == 'checkbox') {
                    if ($(this).is(':checked')) {
                        data[$(this).attr('id')] = true
                    }
                    return
                }
                data[$(this).attr('id')] = $(this).val()
            })
            $.ajax({
                url: '/settings/tags/update/object/' + localStorage.getItem('settingPage'),
                method: 'post',
                data: data,
                error: function (msg) {
                    swal('Ошибка!', msg.responseJSON.message, "error")
                },
                success: function (data) {
                    $('.setting-content').html(data)
                    changes = false
                }
            })

        })
        let changes = false
        $('a').click(function (e) {
            if (changes) {
                e.preventDefault()
                swal({
                    title: "Вы не сохранили данные",
                    icon: "warning",
                    buttons: ['Вернуться', 'Я знаю'],
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            window.location.href = $(this).attr('href')
                        }
                    })
            }
        })

        $('.setting-content').on('input', function () {
            changes = true
        })
        @endif

        $('.setting-page').click(function () {
            let object = $(this).attr('id')
            @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
            if (changes) {
                swal({
                    title: "Вы не сохранили данные",
                    icon: "warning",
                    buttons: ['Вернуться', 'Я знаю'],
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: '/settings/tags/object/' + object,
                                error: function (msg) {
                                    swal('Ошибка!', msg.responseJSON.message, "error")
                                },
                                success: function (data) {
                                    $('.setting-content').html(data)
                                    changes = false
                                    @if(\Illuminate\Support\Facades\Auth::user()->role != 'admin')
                                    $('.setting-content input,textarea').each(function () {
                                        $(this).attr('disabled', true)
                                    })
                                    @endif
                                }
                            })
                            localStorage.setItem('settingPage', object)
                        } else {
                            $('.setting-page#' + localStorage.getItem('settingPage')).prop('checked', true)
                        }
                    })
            } else {
                @endif
                $.ajax({
                    url: '/settings/tags/object/' + object,
                    error: function (msg) {
                        swal('Ошибка!', msg.responseJSON.message, "error")
                    },
                    success: function (data) {
                        $('.setting-content').html(data)
                        @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                            changes = false
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->role != 'admin')
                        $('.setting-content input,textarea').each(function () {
                            $(this).attr('disabled', true)
                        })
                        @endif
                    }
                })
                localStorage.setItem('settingPage', object)

                @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
            }
            @endif
        })

        $('.setting-page#' + localStorage.getItem('settingPage')).click()

        if (!localStorage.getItem('settingPage')) {
            $('.setting-page').first().click()
        }

    </script>
@endsection
