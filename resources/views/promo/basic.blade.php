@extends('layout.sidenav')
@section('content')
    <div class="content" style="">
        <div class="nav nav-pills d-flex justify-content-between">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPromo">
                Добавить акцию
            </button>
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="btnradio" id="all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="all">Общие</label>
                <input type="radio" class="btn-check" name="btnradio" id="archive" autocomplete="off">
                <label class="btn btn-outline-primary" for="archive">Архив</label>
            </div>
        </div>
        @foreach($promos as $promo)
            @if ($loop->index % 3 == 0)
                <div class="row">
                    @endif
                    <div class="card col-12 col-md m-3 p-0 promo-card" value="{{ $promo->id }}">
                        <div class="card-img-top gradient text-bottom">
                            <p class="front">{{ $promo->name }}</p>
                            <img src= "{{ key_exists($promo->name, $photos) ? "/storage/{$photos[$promo->name][0]->path}" : '' }}"
                                 class="promo-img" alt="...">
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                {{ $promo->description }}
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button class="btn btn-secondary promo-demo">Демо</button>
                            <button class="btn btn-secondary promo-edit">Редактировать</button>
                        </div>
                    </div>
                    @if ($loop->index % 3 == 2)
                </div>
            @endif
        @endforeach
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPromo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" enctype="multipart/form-data" method="post" action="/promo">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить акцию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="promo_name">Название</span>
                        <input type="text" class="form-control" placeholder="Введите название..." aria-label="Username"
                               aria-describedby="promo-name" name="promo_name">
                    </div>
                    @include('layout.datepicker', [
                        'pickerId' => 'promoStart',
                        'pickerName' => 'Начало акции',
                        'pickerPlaceholder' => 'Введите дату начала'])
                    @include('layout.datepicker', [
                        'pickerId' => 'promoEnd',
                        'pickerName' => 'Конец акции',
                        'pickerPlaceholder' => 'Введите дату завершения'])
                    <p title="При выборе нескольких файлов, фото будет определяться случайно" class="text-start mb-1">
                        Выберите фото акции:
                    </p>
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" id="promo_images"  name="promo_images[]" multiple>
                    </div>
                    <div class="text-start">
                        <span>Шаблон акции</span>
                    </div>
                    <div class="input-group">
                        <textarea class="form-control" aria-label="With textarea" name="promo_layout" id="promo_layout"></textarea>
                    </div>
                    @include('layout.tags', ['textarea' => 'promo_layout'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" name="submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
        })
    </script>
@endsection
