@if (is_null($cp_post))
    <h4 class="text-start m-1 text-dark">Нет постов</h4>
@else
    @foreach($cp_post as $post)
        <div class="card m-1 mb-4 cp-card" data="{{$post['post']->id}}">
            <div class="card-header align-items-center text-start"
                 @if($post['post']->border || !$post['post']->mute)
                 style="background: #fcf4ab"
                @endif>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title">{{ date('d.m.Y H:i', (strtotime($post['post']->publish_date)))}}
                        - {{$post['type']}}</h5>
                    <div class="buttons">
                        <button class="btn btn-outline-secondary cp-post-edit me-3" id="post-settings">Редактировать
                        </button>
                        <button class="btn btn-outline-danger cp-post-delete" id="delete-post">Убрать из КП</button>
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

            <div class="card-body d-flex p-0 justify-content-between">
                <p class="overflow-hidden text-start cp-description m-3 post-text"
                   style="height: 150px">{{ $post['post']->text }}</p>
                <div id='img-container' class="m-2">
                    @if(empty($post['image']))
                        <img src="/img/template_01.png" alt="" height="150">
                    @else
                        <img src="/{{$post['image']}}" alt="" height="150">
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                {{--            <div class="input-group w-50 me-3" id="cp-type">--}}
                {{--                <label class="input-group-text" for="type-choose">Тип</label>--}}
                {{--                <select class="form-select" id="type-choose">--}}
                {{--                    <option selected>Choose...</option>--}}
                {{--                    <option value="1">One</option>--}}
                {{--                    <option value="2">Two</option>--}}
                {{--                    <option value="3">Three</option>--}}
                {{--                </select>--}}
                {{--            </div>--}}
                <input type="text" class="form-control posts-comment" placeholder="Комментарий к посту"
                       aria-label="Username" aria-describedby="addon-wrapping">
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

<div class="cp-post-edit-modal">
    <div class="modal fade" id="edit-poll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
</div>

<script>
    $('.cp-post-edit').click(function () {
        let post = $(this).closest('.cp-card').attr('data')
        $.ajax({
            url: '/content_plan/editModal/' + post,
            error: function (msg) {
                console.log(msg)
            },
            success: function (data) {
                $('.cp-post-edit-modal .modal').html(data).modal('show')
            }
        })
    })


    $('.cp-post-delete').click(function () {
        let post = $(this).closest('.cp-card').attr('data')
        let cp = $('.cp-radio:checked').attr('id')
        swal({
            title: "Вы уверены?",
            icon: "warning",
            buttons: ['Отмена', 'Удалить'],
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: '/content_plan/post/delete/' + post,
                        method: 'post',
                        data: {cp: cp},
                        error: function (msg) {
                            console.log(msg)
                        },
                        success: function (data) {
                            $('#cp-content').html(data)
                        }
                    })
                    swal("Пост был удалён!", {
                        icon: "success",
                        button: false,
                        timer: 1000
                    });
                }
            })

    })
</script>
