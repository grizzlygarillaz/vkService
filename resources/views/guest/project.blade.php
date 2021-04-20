@extends('layout.page')
@section('sidenav')
    <div class="d-flex justify-content-center">
        <div class="m-3 m-lg-5 w-100 w-lg-75 h-100">
            {{--            <div class="d-flex justify-content-between">--}}
            {{--                <div>--}}
            {{--                    <span class="text-dark text-sm">Сортировка: </span>--}}
            {{--                    <span class="btn btn-sm btn-outline-primary text-sm me-4" id="post-sort"--}}
            {{--                          data-sort="{{$postSort}}">Дата {!!$postSort == 'asc' ? '&#9650;' : '&#9660;'!!}</span>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--            <hr class="text-dark m-2">--}}
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
                            <div class="card-body p-0 post-guest-content">
                                @if(empty($post['image']))
                                    <img class="border" src="/img/template_01.png" alt="">
                                @else
                                    <img class="border" src="/{{$post['image']}}" alt="">
                                @endif
                                <p class="post-text text-sm m-3">{{ $post['text'] }}</p>
                            </div>
                            <div class="card-footer" style="background-color: #b4dcff">
                                <p class="m-0">Комментарий:</p>
                                <div class="row m-0">
                                    <div class="col-xxl-8 col-12 p-0 ">
                                        <div class="input-group bg-light">
                                            <textarea class="form-control comment" placeholder="Введите комментарий"
                                                      rows="1"
                                                      name="comment"
                                                      aria-label="With textarea">{{ $post['post']->comment ?? '' }}</textarea>
                                            <span title="Отправить комментарий"
                                                  class="input-group-text btn btn-success send-comment">
                                                <span class="material-icons">send</span>
                                            </span>
                                        </div>
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
    </div>
    <script>
        $('.send-comment').click(function () {
            let post = $(this).closest('.cp-card').attr('id')
            let comment = $(this).siblings('.comment').val()
            console.log(post, comment)
            $.ajax({
                url: '/guest/comment/send/' + post,
                method: 'post',
                data: {comment: comment},
                error: function (msg) {
                    swal('Ошибка!', msg.responseJSON.message, "error")
                },
                success: function () {
                    swal('Комментарий сохранён', {
                        timer: 1000,
                        buttons: false,
                        icon: 'success'
                    })
                }
            })
        })
    </script>
@endsection
