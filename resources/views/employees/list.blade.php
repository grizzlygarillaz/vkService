@extends('layout.sidenav')
@section('content')

    <div class="content " style="">
        <input class="form-control mb-2 w-75" type="text" id="user-search" placeholder="Поиск...">
        <hr class="m-2 w-75">
        @foreach($users as $user)
            <button type="button" class="btn btn-outline-primary user-profile text-start w-75 mb-3" data-user="{{$user->id}}">{{$user->name}}   |   {{$user->email}}</button>
        @endforeach
    </div>

    <div id="modal-place"></div>
    <script>
        $(document).on('keyup', '#user-search', function () {
            $('.user-profile').each(function () {
                let str = $(this).text().toLowerCase()
                let search = $('#user-search').val().toLowerCase()
                if (~str.indexOf(search)) {
                    $(this).show()
                } else {
                    $(this).hide()
                }
            })
        })

        $('.user-profile').click(function () {
            $.ajax({
                url: '/employees/projects/' + $(this).attr('data-user'),
                error: function (msg) {
                    swal('Ошибка!', msg.responseJSON.message, "error")
                },
                success: function (data) {
                    $('#modal-place').html(data).children('.modal').modal('show')
                }
            })
        })
    </script>
@endsection
