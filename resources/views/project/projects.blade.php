@extends('layout.sidenav')
@section('content')
    <div class="row row-cols-2 my-2">
        <div class="btn-group-vertical pre-scrollable col-3 overflow-auto align-self-baseline"
             id="project-list">
            <input class="form-control mb-2" type="text" id="project-search" placeholder="Поиск...">
            @foreach($projects as $project)
                <input type="radio" class="btn-check" name="project-radio" id="{{ $project['id'] }}" autocomplete="off">
                <label class="btn btn-outline-secondary project" for="{{ $project['id'] }}">{{ $project['name'] }}</label>
            @endforeach
        </div>
        <div class="col-9 ps-0" id="project-info">
            @if(isset($loadErrors))
                <div id="system_error" class="load-error alert alert-danger">
                    @foreach($loadErrors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div class="info card text-start" id="project-info">
                <div class="project-header card-header bg-white d-flex" project="">
                    <h3 id="project-name" style="color: #1d2124; min-width: 30%">Проект</h3>
                    <div class="btn-group project-page mx-5 " role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" checked id="post" autocomplete="off">
                        <label class="btn btn-outline-primary" for="post">Посты</label>

                        <input type="radio" class="btn-check" name="btnradio" id="stories"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="stories">Сторис</label>

                        <input type="radio" class="btn-check" name="btnradio" id="info"
                               autocomplete="off">
                        <label class="btn btn-outline-primary" for="info">Проект</label>

                        @foreach($objectsPage as $key => $item)
                            <input type="radio" class="btn-check" name="btnradio" id="{{$key}}" autocomplete="off">
                            <label class="btn btn-outline-primary" for="{{$key}}">{{$item['name']}}</label>
                        @endforeach

                    </div>
                </div>
                <div class="project-content card-body w-100 " style="min-height: 300px">

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let id = ''

            function project_page(page, id) {
                id = $('.project-header').attr('project')
                let ajaxurl = '/projects/page/' + page + '/' + id
                $.ajax({
                    url: ajaxurl,
                    data: {object: page, local: JSON.stringify(localStorage)},
                    beforeSend: function () {
                        $('input[type=radio]').each(function () {
                            $(this).attr('disabled', true)
                        })
                    },
                    error: function (msg) {
                        swal('Ошибка!', msg.responseJSON.message, "error")
                        $('.project-content').html('');
                        $('input[type=radio]').each(function () {
                            $(this).attr('disabled', false)
                        })
                    },
                    success: function (data) {
                        $data = $(data); // the HTML content that controller has produced
                        $('.project-content').html($data);
                        $('input[type=radio]').each(function () {
                            $(this).attr('disabled', false)
                        })
                    }
                })
            }

            $(document).on('click', '.project', function () {
                id = $(this).attr('for')
                let name = $(this).text()
                $('.project-header').attr('project', id)
                $('#promoInfo').attr('project', $('.project-header').attr('project'))
                $('.project').removeClass('btn-secondary')
                $('.project').addClass('btn-outline-secondary')
                $(this).toggleClass('btn-secondary btn-outline-secondary')
                $('#project-name').text(name)
                $('.popover').each(function () {
                    $(this).remove()
                })
                $('.btn-check:checked').click()
                localStorage.setItem('currentProject', id)
            })

            $(document).on('click', '.project-page input', function () {
                id = $('.project-header').attr('project')
                thisPage = $(this).attr('id');
                $('.popover').each(function () {
                    $(this).remove()
                })
                $('.project-content').html(SPINNER)
                project_page(thisPage, id)
            })

            $(document).on('keyup', '#project-search', function () {
                $('.project').each(function () {
                    let str = $(this).text().toLowerCase()
                    let search = $('#project-search').val().toLowerCase()
                    if (~str.indexOf(search)) {
                        $(this).show()
                    } else {
                        $(this).hide()
                    }
                })
            })

            let currentProject = localStorage.getItem('currentProject') ?
                localStorage.getItem('currentProject')
                :
                $('#project-list .btn-check').first().attr('id')
            $('.project[for=' + currentProject + ']').click()
        })
    </script>

    <script>
        $(document).ready(function () {

            $(document).on('change', '.promo-layout-toggle', function () {
                let post = $(this).closest('.cp-card').attr('id')
                let promo = $(this).closest('div[data=post-promo]').find('#promo-choose option:selected').val()
                let project = $('.project-header').attr('project')
                let check = $(this).prop('checked')
                $.ajax({
                    method: 'post',
                    url: '{{ route('getPromoLayout') }}',
                    data: {project: project, promo: promo, post: post, layout: check},
                    error: function (msg) {
                        console.log(msg)
                    },
                    success: function (data) {
                        $('#post-text-area-' + post).val(data)
                    }
                })
            })
            $(document).on('change', '#promo-choose', function () {
                let post = $(this).closest('.cp-card').attr('id')
                let promo = $('option:selected', this).val()
                if (promo !== 'default') {
                    $.ajax({
                        url: '/projects/post/promo/' + promo,
                        data: {post: post},
                        error: function (msg) {
                            console.log(msg)
                        },
                        success: function (data) {
                            $data = $(data.html)
                            $('#changePhotoPost' + post).attr('id', 'to-remove')
                            $('#to-remove').after($data)
                            $('#to-remove').remove()
                            if (data.photo !== null) {
                                $('.card-body#' + post + ' #img-container .post-img')
                                    .attr('src', '/storage/' + data.photo)
                            } else {
                                $('.card-body#' + post + ' #img-container .post-img')
                                    .attr('src', '/img/template_01.png')
                            }
                            if ($('.promo-layout-toggle#text-' + post).prop('checked') === true) {
                                $('.promo-layout-toggle#text-' + post).click()
                            }
                        }
                    })
                }
            })
        })
    </script>

    <script>
        $(document).ready(function () {

            $('.add-answer').click(function () {
                let answerGroup =
                    `<div class="input-group input-group-sm poll-answer-group mb-2">
                        <input type="text" class="form-control poll-answer" placeholder="Введите ответ"
                               aria-label="Recipient's username" name='answer[]'>
                        <button class="btn btn-outline-danger delete-answer" type="button">✕</button>
                    </div>`
                $(this).before(answerGroup)

                let answerCount = selectorCount('.poll-answer')
                if (answerCount < 10) {
                    $(this).show()
                } else {
                    $(this).hide()
                }
            })
            $(document).on('click', '.delete-answer', (function () {
                $(this).closest('.poll-answer-group').remove()

                let answerCount = selectorCount('.poll-answer')
                if (answerCount < 10) {
                    $('.add-answer').show()
                } else {
                    $('.add-answer').hide()
                }
            }))
            $('#poll-type').change(function () {
                $.ajax({
                    url: '/content_plan/post/tags/' + $('option:selected', this).val(),
                    data: {textarea: 'post-text'},
                    error: function (msg) {
                        console.log(msg)
                    },
                    success: function (data) {
                        $('.tag_list_poll').html(data)
                    }
                })
                if (!$('option:selected', this).val()) {
                    $('#border-toggle').removeClass('d-none')
                } else {
                    $('#border-toggle').addClass('d-none')
                }

            })
        })
    </script>

@endsection
