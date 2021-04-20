<div id="project-story-content">
    <div class="d-flex justify-content-between">
        <div>
            <div id="add-story" class="btn btn-outline-success" data-bs-toggle="modal"
                 data-bs-target="#add-project-story">Добавить сторис
            </div>
        </div>
        <div>
            <span class="text-dark text-sm ml-4">Сортировка: </span>
            <span class="btn btn-sm btn-outline-primary text-sm me-3" id="story-sort"
                  data-sort="{{$storySort}}">Дата {!!$storySort == 'asc' ? '&#9650;' : '&#9660;'!!}</span>
            <a href="{{ $publicStories }}" class="btn btn-outline-success" target="_blank">Управление историями</a>
        </div>
    </div>
    @include('project.add_story_modal')
    <hr class="text-dark m-2">
    <div class="project-data text-dark">
        @if(isset($cpError))
            <div class="alert alert-danger" role="alert">
                {{$cpError}}
            </div>
        @endif
        @if (!isset($stories) or count($stories) == 0)
            <h4 class="text-start m-1 text-dark">Пусто</h4>
        @else
            @foreach($stories  as $story)
                @if(empty($story['story']->vk_id))
                    <div class="card mb-4 cp-card" id="{{ $story['story']->id }}">
                        @foreach($story['error'] as $error)
                            <div class="card-header bg-danger text-white">
                                <p class="p-0 m-0"><strong> {{ $error }} </strong></p>
                            </div>
                        @endforeach
                        <div class="card-header align-items-center text-dark"
                             @if ($story['publish'])
                             style="background: #fcf4ab"
                            @endif>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($story['story']->publish_date)))}}
                                        - {{ $story['object'] }}</h5>
                                    @if ($story['publish'])
                                        <p class="m-0"><strong>В публикации</strong></p>
                                    @endif
                                </div>
                                <div class="buttons">
                                    <button class="btn btn-outline-danger story-delete h-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor"
                                             class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-outline-primary story-settings" id="">Редактировать</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-flex justify-content-between story-content">
                            @if(empty($story['image']))
                                <img class="border" src="/img/template_01.png" alt="" height="150">
                            @else
                                <img class="border" src="/{{$story['image']}}" alt="" height="150">
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between p-3">
                            @if($story['story']->stories_type)
                                @include("stories.story_type", [
                                        'objects' => isset($objects[$story['story']->stories_type]) ? $objects[$story['story']->stories_type] : null,
                                        'selected' => $story['story']->object_id,
                                        'typeName' => $story['object'],
                                        'type' => $story['story']->stories_type
                                    ])
                            @else
                                <p></p>
                            @endif
                            {{--                                {{ ($story['error']) ? 'disabled' : '' }}--}}
                            @if ($story['publish'])
                                <button class="btn btn-primary ml-4 send-story">
                                    Отменить публикацию
                                </button>
                            @else
                                <button
                                    class="btn btn-primary ml-4 send-story" {{$story['story']->error ? 'disabled' : ''}}>
                                    Опубликовать
                                </button>
                            @endif
                        </div>

                        @if($story['users']['author'])
                            <div class="card-footer d-flex justify-content-between">
                                <span class="text-secondary">Автор:
                                    {{$story['users']['author']->name}}
                                    ({{ explode('@', $story['users']['author']->email)[0] }})
                                </span>
                                @if($story['users']['editor'])
                                    <span class="text-secondary">
                                        Редактировал:
                                    {{$story['users']['editor']->name}}
                                    ({{ explode('@', $story['users']['editor']->email)[0] }})
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card m-1 mb-4 cp-card" id="{{ $story['story']->id }}">
                        <div
                            class="card-header d-flex justify-content-between align-items-center text-light bg-success">
                            <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($story['story']->publish_date)))}}
                                - {{ $story['object'] }}</h5>
                            <h4><strong>Опубликован</strong></h4>
                        </div>
                        <div class="card-body d-flex justify-content-between story-content text-muted">
                            @if(empty($story['image']))
                                <img class="border" src="/img/template_01.png" alt="" height="150">
                            @else
                                <img class="border" src="/{{$story['image']}}" alt="" height="150">
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>

<div class="modal fade modal-story-edit" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

<script>
    $('#story-sort').click(function () {
        if ($(this).attr('data-sort') == 'asc') {
            localStorage.setItem('story_sort', 'desc')
        } else {
            localStorage.setItem('story_sort', 'asc')
        }
        $('.project-page input:checked').click()
    })
    $('.send-story').click(function () {
        let card = $(this).closest('.cp-card')
        let id = $('.project-header').attr('project')
        $.ajax({
            url: '/stories/send/' + id,
            data: {story: $(this).closest('.cp-card').attr('id')},
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function () {
                $('.project-page input:checked').click()
            }
        })
    })
    $('.story-delete').click(function () {
        let story = $(this).closest('.cp-card').attr('id')
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
                        url: '/stories/delete/' + story,
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
    $('.story-settings').click(function () {
        let id = $(this).closest('.cp-card').attr('id')
        $.ajax({
            url: '/stories/edit/' + id,
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function (data) {
                console.log(data)
                $('.modal-story-edit').html(data)
                $('.modal-story-edit').modal('show')
            }
        })
    })
</script>
