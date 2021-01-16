@extends('layout.sidenav')
@section('content')
    <div class="content">
        <div class="btn-toolbar d-flex overflow-auto p-1" role="toolbar" aria-label="Toolbar with button groups">
            <button class="btn btn-success me-4" id="add-cp">Добавит КП</button>
            <div class="btn-group" id="cp-list" role="group">
                {!! $content_plan !!}
            </div>
        </div>
        <hr style="color: #bbc5d0" class='m-0 mb-2'>
        <div class="btn-toolbar d-flex p-1 mb-3 justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
            <button class="btn btn-outline-success" id='add-post'>Добавить пост</button>
            <button class="btn btn-outline-danger" id="delete-cp">Удалить КП</button>
        </div>
        <div id="cp-content">
            @include('posts._post')
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
                        <input type="text" id="cp-name" class="form-control" placeholder="Введите название..." aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="modal-add-cp">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    @include('posts.add_post_modal')
    <script>
        $(document).ready(function () {
            let cpModal = new bootstrap.Modal(document.getElementById('cpModal'))
            let addPostModal = new bootstrap.Modal(document.getElementById('add-cp-post'), {
                backdrop: 'static'
            })
            $(document).on('click', '#add-cp', function () {
                cpModal.toggle()
            })

            $(document).on('click', '#modal-add-cp', function () {
                $.ajax({
                    method: 'POST',
                    url: '/content_plan/add',
                    data: {name: $('#cp-name').val(),_token: "{{ csrf_token() }}"},
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

            $(document).on('click', '.cp-radio', function () {
                let id = $(this).attr('id');
                let ajaxurl = '{{route('content_plan', ':id')}}';
                ajaxurl = ajaxurl.replace(':id', id);
                $.ajax({
                    url: ajaxurl,
                    type: "GET",
                    success: function(data){
                        $data = $(data); // the HTML content that controller has produced
                        $('#cp-content').html($data);
                    }
                });
            })

            $(document).on('click', '#add-post', function () {
                addPostModal.toggle()
            })

            $('.cp-radio').first().click()
        })
    </script>
@endsection
