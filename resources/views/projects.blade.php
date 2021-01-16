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
            <div class="info card text-start" id="project-info">
                <div class="project-header card-header bg-white d-flex" project="{{ $project->id }}">
                    <h3 id="project-name" style="color: #1d2124; min-width: 30%">Проект</h3>
                    <div class="btn-group project-page mx-5" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" checked id="project-about"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-about">Проект</label>

                        <input type="radio" class="btn-check" name="btnradio" id="project-post" autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-post">Посты</label>

                        <input type="radio" class="btn-check" name="btnradio" id="project-promo" autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-promo">Акции</label>

                        <input type="radio" class="btn-check" name="btnradio" id="project-dish" autocomplete="off">
                        <label class="btn btn-outline-primary" for="project-dish">Блюда</label>
                    </div>
                </div>
                <div class="project-content card-body" style="min-height: 300px">
                    <div id="project-about-content">
                        <button class="btn btn-outline-dark mb-3 project-edit">Изменить</button>
                        <div class="project-data"></div>
                    </div>

                    <div id="project-post-content">
                        <div class="project-data">
                        </div>
                    </div>
                    <div id="project-promo-content">
                        <header class="d-flex justify-content-between">
                            <button class="btn btn-success mb-3 add-promo">Дабавить акцию</button>
                            <div class="btn-group" style="height: fit-content;" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="promo-radio" id="active-promo" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="active-promo">Действующие</label>

                                <input type="radio" class="btn-check" name="promo-radio" id="archive-promo" autocomplete="off">
                                <label class="btn btn-outline-primary" for="archive-promo">Архивные</label>
                            </div>
                        </header>
                        <div class="project-data"></div>
                    </div>
                    <div id="project-dish-content">
                        <button class="btn btn-outline-dark mb-3 project-edit">Изменить</button>
                        <div class="project-data"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPromo" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                @csrf
                <div class="modal-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button data="locked"  class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#promo-locked" aria-expanded="true" aria-controls="promo-locked">
                                    Общие акции
                                </button>
                            </h2>
                            <div id="promo-locked" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" data="private" type="button" data-bs-toggle="collapse" data-bs-target="#promo-private" aria-expanded="false" aria-controls="promo-private">
                                    Персональные акции
                                </button>
                            </h2>
                            <div id="promo-private" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="add-promo">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="promoInfo" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="promo-data">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="promo_name">Название</span>
                            <input type="text" class="form-control bg-white" placeholder="Введите название..." aria-label="Username"
                                   aria-describedby="promo-name" name="promo_name" readonly>
                        </div>
                        @include('layout.datepicker', [
                            'pickerId' => 'promoStart',
                            'pickerName' => 'Начало акции',
                            'pickerPlaceholder' => 'Введите дату начала',
                            'property' => 'readonly disabled'])
                        @include('layout.datepicker', [
                            'pickerId' => 'promoEnd',
                            'pickerName' => 'Конец акции',
                            'pickerPlaceholder' => 'Введите дату завершения',
                            'property' => 'readonly disabled'])
                        <div class="text-start">
                            <span>Пример поста</span>
                        </div>
                        <div id="promo-example" class="card text-start">
                            <div class="card-body">
                                <span id="promo-layout-text" class=""></span>
                                <div id="layout-img"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" id="delete-promo">Удалить</button>
                    <button type="button" class="btn btn-primary" id="edit-promo">Редактировать</button>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function () {
        let addPromoModal = new bootstrap.Modal(document.getElementById('addPromo'), {
            backdrop: 'static'
        })
        let promoModal = new bootstrap.Modal(document.getElementById('promoInfo'))

        function edit (type) {
            let obj = {}
            $('#project-' + type + '-content .project-data input').each(function () {
                let key = $(this).attr('name')
                obj[key] = $(this).val();
            })
            return obj
        }

        $(document).on('click', '.project', function () {
            $('.save-project-change')
                .text('Изменить')
                .removeClass('save-project-change btn-outline-success')
                .addClass('project-edit  btn-outline-dark')
            $('#promoInfo').attr('project', $('.project-header').attr('project'))
            $('.project').removeClass('btn-secondary')
            $('.project').addClass('btn-outline-secondary')
            $(this).toggleClass('btn-secondary btn-outline-secondary')
            let id = $(this).attr('data')
            let name = $(this).attr('name')
            $('.project-header').attr('project', id)
            $('.project-content').children('div').each(function () {
                $(this).children('div').html('<div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>')
            })
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
                    $('#project-about-content .project-data').html(data.about)
                    $('#project-promo-content .project-data').html(data.promo)
                    $('#project-post-content .project-data').html(data.posts)
                    $('#project-name').text(name)
                }
            })

        })

        $(document).on('click', '.project-page input', function () {
            $('.project-content').children('div').each(function () {
                $(this).hide()
            })
            $('#' + $(this).attr('id') + '-content').show()
        })

        $(document).on('click', '.promo-toggle', function () {
            let promo = $(this).parent('div').attr('id')
            let project = $('.project-header').attr('project')
            console.log(promo, project)
            $.ajax({
                url: 'promo/' + promo + '/' + project,
                data: {promo: promo},
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('.promo-data input[name=promo_name]').prop('value', data.promo.name)
                    $('#promoStart').prop('value', data.start)
                    $('#promoEnd').prop('value', data.end)
                    $('#promo-layout-text').text(data.text)
                    if (data.img != null) {
                        $('#layout-img').html(
                            '<img src="' + data.img + '" style="object-fit: contain; height: 100%; width: 100%">'
                        )
                    }
                }
            })
            promoModal.toggle()

            $('#edit-promo').click(function () {
                window.location.replace('/promo/edit/' + promo)
            })

            $('#delete-promo').click(function () {
                $.ajax({
                    method: 'POST',
                    url: '/projects/remove_promo',
                    data: {promo: promo, project: project},
                    error: function (e) {
                        console.log(e)
                    },
                    success: function () {
                        promoModal.hide()
                        $('button.project[data=' + project + ']').click()
                    }
                })
            })
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

        $(document).on('click', '#add-promo', function () {
            let project = $('.project-header').attr('project')
            let promo = {}
            let count = 0;
            $('#promo-locked .accordion-body input').each(function () {
                if ($(this).prop('checked') == true)
                {
                    promo[count] = $(this).val()
                    count++
                }
            })
            $.ajax({
                method: 'POST',
                url: '/projects/add_promo',
                data: {project: project, promos: promo},
                statusCode: {
                    function(e) {
                        console.log(e)
                    }
                },
                success: function () {
                    let project = $('.project-header').attr('project')
                    addPromoModal.hide()
                    $('button.project[data=' + project + ']').click()
                }
            })
        })

        $(document).on('click', '.project-edit', function () {
            let project = $('.project-header').attr('project')
            $('#project-about-content .project-data .input-group').each(function() {
                $('input', this).removeAttr('readonly').prop('placeholder', 'Введите данные...')
                $('span', this).addClass('bg-secondary text-white').css('transition', '0.5s')
            })
            $(this)
                .text('Сохранить изменения')
                .removeClass('project-edit  btn-outline-dark')
                .addClass('save-project-change btn-outline-success')
        })

        $(document).on('click', '.save-project-change', function () {
            let project = $('.project-header').attr('project')
            $.ajax({
                method: 'POST',
                url: 'projects/edit/' + project,
                data: edit('about'),
                error: function (msg) {
                    console.log(msg)
                },
                success: function () {
                    $('.save-project-change')
                        .text('Изменить')
                        .removeClass('save-project-change btn-outline-success')
                        .addClass('project-edit  btn-outline-dark')
                    $('.project').first().click()
                }
            })
        })

        $(document).on('click', '#publish-post', function () {
            let post = $(this).attr('value')
            let project = $('.project-header').attr('project')
            let photo = $('#promo_images').val()
            console.log(photo)
            console.log(post, project, photo)
            // $.ajax({
            //     url: '/posts/sendGroupPost',
            //     method: 'post',
            //     data: {project: project, post: post, photo: photo},
            //     error: function (msg) {
            //         console.log(msg)
            //     }
            // })
        })

        $(document).on('click', 'button.add-promo', function () {
            let id = $('.project-header').attr('project')
            $.ajax({
                url: '/promo/for_project',
                data: {id: id},
                dataType: 'json',
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    console.log(data)
                    $('#promo-locked .accordion-body').html(data.available_promo)
                }
            })
            addPromoModal.toggle()
        })
        $('.project').first().click()
        $('.project-page input').first().click()
    })
</script>
@endsection
