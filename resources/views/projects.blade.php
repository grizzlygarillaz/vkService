@extends('layout.sidenav')
@section('content')
    <div class="row my-2">
        <div class="btn-group-vertical pre-scrollable col-6 overflow-auto col-md-2 align-self-baseline" id="project-list">
            <input class="form-control mb-2" type="text" id="project-search" placeholder="Поиск...">
            @foreach($projects as $project)
                <button class="btn btn-outline-secondary project" style="height: auto" aria-current="page"
                        data="{{ $project->id }}" name="{{$project->name}}">{{$project->name}}</button>
            @endforeach
        </div>
        <div class="col ps-0" id="project-info">
            <div class="info card text-start">
                <div class="project-header card-header bg-white d-flex" project="{{ $project->id }}">
                    <h3 id="project-name" style="color: #1d2124; min-width: 30%">Проект</h3>
                    <div class="btn-group project-page mx-5" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" checked id="project-about"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-about">Проект</label>

                        <input type="radio" class="btn-check" name="btnradio" id="project-promo" autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-promo">Акции</label>

                        <input type="radio" class="btn-check" name="btnradio" id="project-dish" autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-dish">Блюда</label>
                    </div>
                </div>
                <div class="project-content card-body" style="min-height: 300px">
                    <div id="project-about-content">
                        <div id="project-data"></div>
                        <button id="project-edit" class="btn btn-outline-dark mt-3">Изменить</button>
                    </div>
                    <div id="project-promo-content">

                    </div>
                    <div id="project-dish-content">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $(document).on('click', '.project', function () {
                $('.project').removeClass('btn-secondary')
                $('.project').addClass('btn-outline-secondary')
                $(this).toggleClass('btn-secondary btn-outline-secondary')
                let id = $(this).attr('data')
                let name = $(this).attr('name')
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/projects/info',
                    data: {id: id},
                    dataType: 'json',
                    error: function (msg) {
                        console.log(msg)
                    },
                    success: function (data) {
                        $('#project-data').html(data.about)
                        $('#project-name').text(name)
                    }
                })

            })

            $(document).on('click', '.project-page input', function () {
                $('.project-content').children().each(function () {
                    $(this).hide()
                })
                $('#' + $(this).attr('id') + '-content').show()
            })

            $(document).on('keyup', '#project-search', function () {
                $('.project').each(function () {
                    let str = $(this).attr('name').toLowerCase()
                    let search = $('#project-search').val().toLowerCase()
                    if (~str.indexOf(search)) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
            })

            $('.project-page').first().click()
            $('.project').first().click()

        })
    </script>
@endsection
