<div id="project-post-content">
    <div class="d-flex justify-content-between">
        <div>
            <button class="btn btn-success me-2" title='Опубликавть все посты с пометкой "Публиковать"'
                    id="send-all-post">
                Опубликовать все
            </button>
            <button class="btn btn-outline-success" id="add-post">Добавить пост</button>
        </div>
        <div>
            <span class="text-dark text-sm">Сортировка: </span>
            <span class="btn btn-sm btn-outline-primary text-sm me-4" id="post-sort"
                  data-sort="{{$postSort}}">Дата {!!$postSort == 'asc' ? '&#9650;' : '&#9660;'!!}</span>
            <a href="{{ $deferredPosts }}" class="btn btn-outline-success" target="_blank">Отложенные</a>
        </div>
    </div>
    <hr class="text-dark m-2">
    <div class="project-data text-dark">
        @if(isset($cpError))
            <div class="alert alert-danger" role="alert">
                {{$cpError}}
            </div>
        @endif
        @if (!isset($posts) or count($posts) == 0)
            <h4 class="text-start m-1 text-dark">Нет постов</h4>
        @else
            @foreach($posts as $post)
                @if(is_null($post['post']->vk_id))
                    <div class="card mb-4 cp-card" id="{{ $post['post']->id }}">
                        @if($post['error'])
                            @foreach($post['error'] as $error)
                                <div class="card-header bg-danger text-white">
                                    <p class="p-0 m-0"><strong> {{ $error }} </strong></p>
                                </div>
                            @endforeach
                        @endif
                        <div class="card-header align-items-center text-dark"
                             @if($post['post']->border || !$post['post']->mute)
                             style="background: #fcf4ab"
                            @endif>
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($post['post']->publish_date)))}}
                                    - {{ $post['object'] }}</h5>
                                <div class="buttons">
                                    <button class="btn btn-outline-danger post-delete h-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor"
                                             class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-outline-primary post-settings h-100" id="">Редактировать
                                    </button>
                                </div>
                            </div>
                            @if ($post['post']->comment)
                                <p class="badge rounded-pill m-0 bg-white border-info border text-dark fst-italic"
                                   style="font-weight: 500">Комментарий</p>
                            @endif
                            @if ($post['post']->border)
                                <p class="badge rounded-pill m-0 bg-light border text-dark fst-italic"
                                   style="font-weight: 500">Виньетка</p>
                            @endif
                            @if (!$post['post']->mute)
                                <p class="badge rounded-pill m-0 bg-light border text-dark fst-italic"
                                   style="font-weight: 500">Уведомление</p>
                            @endif
                            @if ($post['post']->poll)
                                <span class="badge rounded-pill m-0 bg-primary fst-italic">Опрос</span>
                            @endif
                        </div>
                        <div class="card-body d-flex justify-content-between post-content">
                            <p class="post-text">{{ $post['text'] }}</p>
                            @if(empty($post['image']))
                                <img class="border" src="/img/template_01.png" alt="" height="150">
                            @else
                                <img class="border" src="/{{$post['image']}}" alt="" height="150">
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-end p-3">
                            @if($post['post']->post_type)
                                @include("posts.post_type", [
                                        'objects' => isset($objects[$post['post']->post_type]) ? $objects[$post['post']->post_type] : null,
                                        'selected' => $post['post']->object_id,
                                        'typeName' => $post['object'],
                                        'type' => $post['post']->post_type
                                    ])
                            @else
                                <p></p>
                            @endif
                            <div class="d-flex">
                                <div class="form-check ms-3 align-self-center">
                                    <input class="form-check-input post_to_publish" name="to_publish"
                                           {{$post['error'] ? 'disabled' : 'checked'}} type="checkbox"
                                           value="{{$post['post']->id}}" id="post_{{$post['post']->id}}">
                                    <label class="form-check-label" for="post_{{$post['post']->id}}">
                                        Публиковать
                                    </label>
                                </div>
                                <button class="btn btn-primary ml-4 send-post" {{ ($post['error']) ? 'disabled' : '' }}>
                                    Опубликовать
                                </button>
                            </div>
                        </div>
                        @if($post['post']->comment)
                            <div class="card-footer" style="background-color: #acd4ff">
                                <p class="mb-1"><strong>Комментарий клиента:</strong></p>
                                <p class="m-0">{{$post['post']->comment}}</p>
                            </div>
                        @endif
                        @if($post['users']['author'])
                            <div class="card-footer d-flex justify-content-between">
                                <span class="text-secondary">Автор:
                                    {{$post['users']['author']->name}}
                                    ({{ explode('@', $post['users']['author']->email)[0] }})
                                </span>
                                @if($post['users']['editor'])
                                    <span class="text-secondary">
                                        Редактировал:
                                    {{$post['users']['editor']->name}}
                                    ({{ explode('@', $post['users']['editor']->email)[0] }})
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card m-1 mb-4 cp-card" id="{{ $post['post']->id }}">
                        <div
                            class="card-header d-flex justify-content-between align-items-center text-light bg-success">
                            <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($post['post']->publish_date)))}}
                                - {{ $post['object'] }}</h5>

                            <h4><strong>Опубликован</strong></h4>
                        </div>
                        <div class="card-body d-flex justify-content-between post-content text-muted">
                            <p class="post-text">{{ $post['text'] }}</p>
                            @if(empty($post['image']))
                                <img class="border" src="/img/template_01.png" alt="" height="150" width="150">
                            @else
                                <img class="border" src="/{{$post['image']}}" alt="" height="150" width="150">
                            @endif
                        </div>
                        @if($post['post']->comment)
                            <div class="card-footer" style="background-color: #acd4ff">
                                <p class="mb-1"><strong>Комментарий клиента:</strong></p>
                                <p class="m-0">{{$post['post']->comment}}</p>
                            </div>
                        @endif
                        @if($post['users']['author'])
                            <div class="card-footer d-flex justify-content-between">
                                <span class="text-secondary">Автор:
                                    {{$post['users']['author']->name}}
                                    ({{ explode('@', $post['users']['author']->email)[0] }})
                                </span>
                                @if($post['users']['editor'])
                                    <span class="text-secondary">
                                        Редактировал:
                                    {{$post['users']['editor']->name}}
                                    ({{ explode('@', $post['users']['editor']->email)[0] }})
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>

<div class="modal fade modal-post-edit" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

<div class="place-modal"></div>
<script>
    $('#post-sort').click(function () {
        if ($(this).attr('data-sort') == 'asc') {
            localStorage.setItem('post_sort', 'desc')
        } else {
            localStorage.setItem('post_sort', 'asc')
        }
        $('.project-page input:checked').click()
    })
    $('#add-post').click(function () {
        $.ajax({
            url: '/projects/modal/add-post/' + $('.project-header').attr('project'),
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function (data) {
                $('.place-modal').html(data)
                $('.place-modal .modal').modal('show')
            }
        })
    })
    $('.post-delete').click(function () {
        let post = $(this).closest('.cp-card').attr('id')
        swal({
            title: "Вы уверены?",
            icon: "warning",
            buttons: ['Отмена', 'Да'],
            dangerMode: true,
        })
            .then((willSend) => {
                if (willSend) {
                    $.ajax({
                        method: 'post',
                        url: '/posts/delete/' + post,
                        error: function (msg) {
                            swal('Ошибка!', msg.responseJSON.message, "error")
                        },
                        success: function () {
                            $('.project-page input:checked').click()
                        }
                    })
                }
            })

    })
    $('.send-post').click(function () {
        let data = {}
        data['project'] = $('.project-header').attr('project')
        let post = $(this).closest('.cp-card').attr('id')
        $.ajax({
            method: 'post',
            url: '/projects/post/send/' + post,
            data: data,
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function (data) {
                $('.project-page input:checked').click()
            }
        })
    })

    $('#send-all-post').click(function () {
        let posts = []
        $('.post_to_publish:checked').each(function () {
            posts.push($(this).val())
        })
        let data = {project: $('.project-header').attr('project'), posts: posts}
        swal({
            title: "Все посты проверены?",
            icon: "warning",
            buttons: ['Нет', 'Да'],
            dangerMode: true,
        })
            .then((willSend) => {
                if (willSend) {
                    $.ajax({
                        method: 'post',
                        url: '/projects/post/send/all',
                        data: data,
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Подождите...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading()
                                },
                            })
                        },
                        error: function (msg) {
                            swal.close()
                            Swal.close()
                            $('.project-page input:checked').click()
                            swal('Ошибка!', msg.responseJSON.message, "error")
                        },
                        success: function (data) {
                            swal.close()
                            Swal.close()
                            $('.project-page input:checked').click()
                            swal("Посты отправлены!", {
                                icon: "success",
                                button: false,
                                timer: 1000
                            });
                        }
                    })
                }
            })
    })
    $('.post-settings').click(function () {
        let post = $(this).closest('.cp-card').attr('id')
        $.ajax({
            url: '/posts/edit/' + post,
            error: function (msg) {
                console.log(msg)
            },
            success: function (data) {
                $('.modal-post-edit').html(data)
                $('.modal-post-edit').modal('show')
            }
        })
    })

</script>


<script>
    $('.select-object').change(function () {
        let data = {}
        data['object'] = $('option:selected', this).val()
        data['post'] = $(this).closest('.cp-card').attr('id')
        data['type'] = $(this).attr('name')
        let id = $('.project-header').attr('project')
        $.ajax({
            url: '/projects/post/selectType/' + id,
            data: data,
            error: function (msg) {
                console.log(msg)
            },
            success: function (data) {
                console.log(data)
                $('.project-page input:checked').click()
            }
        })
    })
</script>
