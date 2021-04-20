@extends('layout.sidenav')
@section('content')
    <div class="content">
        <div class="btn-toolbar d-flex overflow-auto p-1 m-0" role="toolbar" aria-label="Toolbar with button groups">
            <button class="btn btn-success me-4" id="add-cp">Добавит КП</button>
            <div class="btn-group" id="cp-list" role="group">
                {!! $content_plan !!}
            </div>
        </div>
        <hr class="text-secondary m-2">
        <div class="btn-toolbar d-flex p-1 justify-content-between" role="toolbar"
             aria-label="Toolbar with button grou ps">
            <div class="add-block">
                <button class="btn btn-outline-success me-3" id='add-post'>Добавить пост</button>
                @include('posts.polls.polls_modal')
                <button class="btn btn-outline-success" id='add-stories'>Добавить сторис</button>
            </div>
            <button class="btn btn-outline-danger" id="delete-cp">Удалить КП</button>
        </div>
        <hr class="text-secondary m-2">
        <div class="btn-group d-flex w-50" role="group" id="cp-page-radio" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check cp-page" name="cp-page" value="posts" id="posts" autocomplete="off"
                   checked>
            <label class="btn btn-outline-primary" for="posts">Посты</label>

            <input type="radio" class="btn-check cp-page" name="cp-page" value="stories" id="stories"
                   autocomplete="off">
            <label class="btn btn-outline-primary" for="stories">Сторис</label>
        </div>
        <hr class="text-secondary m-2">
        <div id="cp-content">
        </div>
    </div>

    <div class="modal fade" id="cpModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Название КП</span>
                        <input type="text" id="cp-name" class="form-control" placeholder="Введите название..."
                               aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="modal-add-cp">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    @include('posts.add_post_modal')
    @include('stories.add_modal')
    <script>
        function checkPage () {
            console.log(localStorage.getItem('cp-page'), localStorage.getItem('cp-radio'))
            if (! localStorage.getItem('cp-page')){
                localStorage.setItem('cp-page', $('.cp-page').first().attr('id'))
            }
            if (! localStorage.getItem('cp-radio')){
                localStorage.setItem('cp-radio', $('.cp-radio').first().attr('id'))
            }
            console.log(localStorage.getItem('cp-page'), localStorage.getItem('cp-radio'))
        }
        $(document).ready(function () {
            let cpModal = new bootstrap.Modal(document.getElementById('cpModal'))
            let addPostModal = new bootstrap.Modal(document.getElementById('add-cp-post'), {
                backdrop: 'static'
            })
            $(document).on('click', '#add-cp', function () {
                cpModal.toggle()
            })
            $(document).on('click', '#add-stories', function () {
                $('#add-cp-story').modal('show')
            })

            $(document).on('click', '#modal-add-cp', function () {
                $.ajax({
                    method: 'POST',
                    url: '/content_plan/add',
                    data: {name: $('#cp-name').val(), page: $('[name=cp-page]').val()},
                    error: function (msg) {
                        console.log(msg)
                    },
                    success: function (data) {
                        $('#cp-list').html(data.content_plan)
                        $('[name=btn-cp]').first().click()
                        cpModal.hide()
                    }
                })
            })

            function cpContent(cpId, page) {
                $('.btn').each(function () {
                    $(this).addClass('disabled')
                })
                let ajaxurl = '{{route('content_plan', ':id')}}';
                $('#cp-content').html(SPINNER)
                ajaxurl = ajaxurl.replace(':id', cpId);
                $.ajax({
                    url: ajaxurl,
                    type: "GET",
                    data: {page: page},
                    success: function (data) {
                        $('.btn').each(function () {
                            $(this).removeClass('disabled')
                        })
                        $('#cp-content').html(data);
                    }
                });
            }

            $(document).on('click', '.cp-page', function () {
                cpContent(localStorage.getItem('cp-radio'), $(this).attr('id'))
                localStorage.setItem('cp-page', $('.cp-page:checked').attr('id'))
            })

            $(document).on('click', '.cp-radio', function () {
                let id = $(this).attr('id');
                cpContent(id, localStorage.getItem('cp-page'));
                $('.cp-identifier').each(function () {
                    $(this).attr('value', id)
                })
                localStorage.setItem('cp-radio', id)
            })

            $('#delete-cp').click(function () {

            })

            $(document).on('click', '#add-post', function () {
                $('#cp-identifier').val($('.cp-radio:checked').attr('id'))
                addPostModal.show()
            })

            $(document).on('click', '.story-settings', function () {
                let id = $(this).closest('.cp-card').attr('id')
                $.ajax({
                    url: '/content_plan/stories/edit/' + id,
                    error: function (msg) {
                        swal('Ошибка!', msg.responseJSON.message, "error")
                    },
                    success: function (data) {
                        $('.modal-story-edit').html(data)
                        $('.modal-story-edit').modal('show')
                    }
                })
            })
            $(document).on('click', '.story-delete', function () {
                let id = $(this).closest('.cp-card').attr('id')
                swal({
                    title: "Вы уверены?",
                    icon: "warning",
                    buttons: ['Отмена', 'Удалить'],
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: '/content_plan/stories/delete/' + id,
                                error: function (msg) {
                                    swal('Ошибка!', msg.responseJSON.message, "error")
                                },
                                success: function (data) {
                                    $('.cp-page#' + localStorage.getItem('cp-page')).click()
                                }
                            })
                            swal("Сторис был удалён!", {
                                icon: "success",
                                button: false,
                                timer: 1000
                            });
                        }
                    })

            })
            checkPage()
            $('.cp-radio#' + localStorage.getItem('cp-radio')).click()
            $('.cp-page#' + localStorage.getItem('cp-page')).click()
        })
    </script>
@endsection
