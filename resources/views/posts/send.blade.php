@extends('layout.sidenav')
@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form action="/post" method="post" class="post-form">
        @csrf
        <div class="input-group mb-3">
            <label class="input-group-text" for="promo">Акция</label>
            <select class="form-select" id="promo" name="promo">
                <option name="promo" selected>Выберите акцию...</option>
                @foreach($promos as $promo)
                    <option value="{{ $promo->id }}">{{ $promo->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group mb-3">
            <label for="publishDate" class="input-group-text" id="basic-addon1">Дата</label>
            <input type="text" id="publishDate" name="publishDate" readonly class="datepicker-here form-control bg-white" placeholder="Выберите дату публикации..." data-timepicker="true">
        </div>
        <div class="card">
            <div class="card-header">
                Шаблон поста
            </div>
            <div class="card-body p-0">
                        <textarea class="form-control" style="height: 200px" name="message"
                                  aria-label="With textarea" id="message" placeholder="Введите текст..."></textarea>
            </div>
            <div class="card-footer">
                <p class="text-muted m-0">Теги автозамены:</p>
                <div class="container p-0 m-0">
                    <div class="row row-cols-1 row-cols-md-auto">
                        @if(isset($tags))
                            @foreach($tags as $tag)
                                <div class="col m-1 tags input-group d-flex" style="width: auto !important;"
                                     data="{{ $tag->tag}}">
                                    <button type="button"
                                            class="btn btn-sm btn-secondary p-1">{{ $tag->tag}}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                          style="font-weight: 400; font-family: 'Roboto', sans-serif">{{ $tag->description }}</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-secondary" id="submit-button">
                Отправить
            </button>
        </div>
    </form>
    <script>

        $(document).ready(function () {
            $(document).on('click', '.tags', function () {
                let textarea = $('[name = "message"]').val()
                $('[name = "message"]').val(textarea + $(this).attr('data'))
            })

            $('textarea.form-control').on('input', function (e) {
                this.style.height = '200px';
                this.style.height = (this.scrollHeight + 6) + 'px';
            });

            // $(document).on('click', '#submit-button', function () {
            //     let message = $('#message').val()
            //     let promo = $('#promo option:selected').val()
            //     let token = $('[name="_token"]').val()
            //     let publishDate = $('#publishDate').val()
            //     $.ajax({
            //         type: 'POST',
            //         url: "/post",
            //         data: {_token: token, message: message, promo: promo, publishDate: publishDate},
            //         error: function (msg) {
            //             console.log(msg)
            //         }
            //     })
            //     $(':input').val('');
            //     $("#promo").val($("#promo option:first").val());
            // })

            $('#publishDate').datepicker({
                minDate: new Date(),
                minHours: new Date('H'),
                minMinutes: new Date('i')
            })
        })
    </script>
@endsection

