<div class="modal fade text-dark" id="{{ $table }}Info" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true"></div>
<div id="project-{{$table}}-content">
    <header class="d-flex justify-content-between">
        <button class="btn btn-success mb-3 add-{{$table}}" data-bs-toggle="modal" data-bs-target="#add-{{$table}}">
            Дабавить
        </button>
        <div class="btn-group" style="height: fit-content;" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="{{$table}}-radio" id="active" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="active">Действующие</label>
            <input type="radio" class="btn-check" name="{{$table}}-radio" id="archive" autocomplete="off">
            <label class="btn btn-outline-primary" for="archive">Архивные</label>
        </div>
    </header>
    <div class="project-data">
        @if(empty($objects))
            <p class="text-dark">Пусто</p>
        @else
            @foreach($objects as $object)
                <div id="{{$object->id}}" class="input-group mb-3 {{$table}}-info">
                    <span class="flex-grow-1 btn btn-outline-primary text-start {{$table}}-toggle">
                        {{$object->name}} {{isset($object->ves) ? "| $object->ves " : ''}} {{isset($object->price) ? "| $object->price руб." : ''}}
                    </span>
                    <button class="btn btn-outline-danger material-icons delete-object">delete</button>
                </div>
            @endforeach

            <script>
                $('.{{$table}}-toggle').click(function () {
                    let id = $(this).closest('.{{$table}}-info').attr('id')
                    $.ajax({
                        url: '/projects/object/modal/{{$table}}',
                        data: {id: id},
                        error: function (msg) {
                            console.log(msg)
                        },
                        success: function (data) {
                            $('#{{ $table }}Info').html(data)
                            $('#{{ $table }}Info').modal('show')
                        }
                    })
                })
            </script>
        @endif
    </div>
</div>

<div class="modal fade" id="add-{{$table}}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data"
              action="/projects/save/object/{{$table}}">
            <div class="modal-body">
                @csrf
                <input type="text" hidden id="project-id" class="project-id" name="project">
                <div class="btn-group d-flex justify-content-center mb-3" role="group">
                    <input type="radio" class="btn-check" name="input_{{$table}}_type" id="load-{{$table}}" value="load"
                           autocomplete="off">
                    <label class="btn btn-outline-secondary" for="load-{{$table}}">Загрузить</label>
                </div>
                <div id="input-{{$table}}">
                    <p class="text-danger">Внимание!!! Старые данные по текущему проекту будут удалены. Будьте
                        осторожны!</p>
                    <div class="input-group">
                        <input type="file" class="form-control" id="{{$table}}-csv-file" name="{{$table}}_csv"
                               aria-label="Upload">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-add-{{$table}}" class="btn btn-secondary" data-bs-dismiss="modal">Отмена
                </button>
                <button type="submit" class="btn btn-success" id="save_{{$table}}">Сохранить</button>
            </div>
        </form>
    </div>
</div>


<script>
    $('.project-id').val($('.project-header').attr('project'))

    $('#save_{{$table}}').click(function () {
        $('#close-add-{{$table}}').click()
        $("*").addClass("disabled")
        $('#project-{{$table}}-content header').html(SPINNER)
    })

    $('[name={{$table}}-radio]').change(function () {
        let id = $('.project-header').attr('project')
        $.ajax({
            url: '/projects/object/{{$table}}/page',
            data: {type: $(this).attr('id'), project: id},
            error: function (msg) {
                console.log(msg)
            },
            success: function (data) {
                $('#project-{{$table}}-content .project-data').html(data)
            }
        })
    })

    $('.delete-object').click(function () {
        let id = $(this).closest('.{{$table}}-info').attr('id')
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
                        url: '/object/delete/{{$table}}/' + id,
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
</script>
