@extends('layout.sidenav')
@section('content')
    <div class="content justify-content-center">
        <form action="/import/save" method="post" class="bg-white rounded mb-4" enctype="multipart/form-data">
            @csrf
            <div class="input-group">
                <input type="file" class="form-control" id="inputGroupFile04" name="csv"
                       aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                <button class="btn btn-success" type="submit" id="inputGroupFileAddon04">Импорт</button>
            </div>
        </form>
{{--        <form action="/project/registration" method="post">--}}
{{--            <h5 class="text-start">Зарегистрировать группы:</h5>--}}
{{--            <div class="input-group">--}}
{{--                @csrf--}}
{{--                <input type="text" class="form-control" placeholder="Введите код"--}}
{{--                       title="Введите код, полученный в адресной строке, после загрузки" id="code-register"--}}
{{--                       name="code" aria-describedby="register" aria-label="Upload">--}}
{{--                <button class="btn btn-outline-primary" type="submit" id="register">Продолжить</button>--}}
{{--            </div>--}}
{{--        </form>--}}
    </div>
@endsection
