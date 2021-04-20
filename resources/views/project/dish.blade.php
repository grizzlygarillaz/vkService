div id="project-dish-content">
    <header class="d-flex">
        <button class="btn btn-success mb-3 add-dish me-3" data-bs-toggle="modal" data-bs-target="#add-dish">
            Дабавить блюдо
        </button>
        <button class="btn btn-outline-secondary mb-3 dish-category" data-bs-toggle="modal"
                data-bs-target="#dish-category">
            Разделы
        </button>
    </header>
    <div class="project-data">
        @if(empty($dishs))
            <p class="text-dark">Нет блюд</p>
        @else
            @foreach($dishs as $dish)
                <div id="{{$dish->id}}" class="input-group mb-3 dish-info">
                    <span class="flex-grow-1 btn btn-outline-primary text-start dish-toggle">{{$dish->name}} | {{$dish->weight ?: '-'}} | {{$dish->new_price }} руб.</span>
                </div>
            @endforeach
        @endif
    </div>
</div>

<div class="modal fade" id="add-dish" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data" action="/projects/dish/save">
            <div class="modal-body">
                @csrf
                <input type="text" hidden id="project-id" class="project-id" name="project">
                <div class="btn-group d-flex justify-content-center mb-3" role="group">
                    {{--                    <input type="radio" class="btn-check" name="input_dish_type" id="write-dish" value="write" autocomplete="off" checked>--}}
                    {{--                    <label class="btn btn-outline-secondary" for="write-dish">Ввести</label>--}}

                    <input type="radio" class="btn-check" name="input_dish_type" id="load-dish" value="load"
                           autocomplete="off">
                    <label class="btn btn-outline-secondary" for="load-dish">Загрузить</label>
                </div>
                <div id="input-dish">
                    @include('project.dish_load.load_file')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-add-dish" class="btn btn-secondary" data-bs-dismiss="modal">Отмена
                </button>
                <button type="submit" class="btn btn-success" id="save_dish">Сохранить</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="dish-category" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="modal-header" method="post" enctype="multipart/form-data" action="/dish/types/upload">
                @csrf
                <input type="text" hidden id="project-id" class="project-id" name="project">
                <div class="input-group">
                    <input type="file" class="form-control" id="dish-type-file" name="csv_types"
                           aria-describedby="load-type-submit" aria-label="Файл">
                    <button class="btn btn-outline-secondary" type="submit" id="load-type-submit">Загрузить</button>
                </div>
            </form>
            <div class="modal-body">
                @if(empty($dishTypes))
                    <p class="text-dark">Нет блюд</p>
                @else
                    @foreach($dishTypes as $type)
                        <div id="{{$type->id}}" class="input-group mb-3 dish-info">
                            <span class="flex-grow-1 btn btn-outline-primary text-start dish-toggle">{{$type->name}}</span>
                        </div>
                    @endforeach
                @endif
            <div class="modal-footer">
                <button type="button" id="close-add-dish" class="btn btn-secondary" data-bs-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.project-id').val($('.project-header').attr('project'))
    {{--
        $('[name=input_dish_type]').click(function () {
            $('#input-dish').html(SPINNER)
            $.ajax({
                url: '/projects/modal/add_dish',
                data: {type: $(this).attr('id')},
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('#input-dish').html(data)
                }
            })
        })
    --}}
    $('.dish-category').click(function () {

    })

    $('#save_dish').click(function () {
        $('#close-add-dish').click()
        $("*").addClass("disabled")
        $('#project-dish-content header').html(SPINNER)
    })
</script>
