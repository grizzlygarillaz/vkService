@extends('layout.page')
@section('sidenav')
<div id="project-post-content">
    <div class="d-flex justify-content-between">
        <div>
            <span class="text-dark text-sm">Сортировка: </span>
            <span class="btn btn-sm btn-outline-primary text-sm me-4" id="post-sort"
                  data-sort="{{$postSort}}">Дата {!!$postSort == 'asc' ? '&#9650;' : '&#9660;'!!}</span>
        </div>
    </div>
    <hr class="text-dark m-2">
    <div class="project-data text-dark">
        @if (!isset($posts) or count($posts) == 0)
            <h4 class="text-start m-1 text-dark">Нет постов</h4>
        @else
            @foreach($posts as $post)
                <div class="card mb-4 cp-card" id="{{ $post['post']->id }}">
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
                                <button class="btn btn-outline-primary post-settings" id="">Редактировать</button>
                            </div>
                        </div>
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
                            <div class="input-group">
                                <span class="input-group-text">Комментарий</span>
                                <textarea class="form-control" rows="2" name="comment"
                                          aria-label="With textarea"></textarea>
                            </div>
                        </div>
                    </div>
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

            @endforeach
        @endif
    </div>
</div>

<script>

    $('#post-sort').click(function () {
        if ($(this).attr('data-sort') == 'asc') {
            localStorage.setItem('post_sort', 'desc')
        } else {
            localStorage.setItem('post_sort', 'asc')
        }
        $('.project-page input:checked').click()
    })
</script>
@endsection
