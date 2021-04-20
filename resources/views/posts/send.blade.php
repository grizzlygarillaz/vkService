@extends('layout.sidenav')
@section('content')
    <div class="content" style="">
        <div class="post-form">
            @csrf
            <div class="input-group mb-3">
                <label class="input-group-text" for="promo">Акция</label>
                <select class="form-select" id="promo" name="promo">
                    <option name="promo" class="disabled" disabled selected value="">Выберите акцию...</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->name }}</option>
                    @endforeach
                </select>
            </div>
            @include('layout.datepicker', [
                'pickerId' => 'publishDate',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации'])
            <div class="card">
                <div class="card-header">
                    Шаблон поста
                </div>
                <div class="card-body p-0">
                        <textarea class="form-control" style="height: 200px" name="message"
                                  aria-label="With textarea" id="message" placeholder="Введите текст..."></textarea>
                </div>
                <div class="card-footer">
                    @include('layout.tags', ['textarea' => 'message'])
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-secondary text-center" id="submit-button">
                    Отправить
                </button>
            </div>
        </div>
        <div id="cover-spin"></div>
    </div>
    <script>

        $(document).ready(function () {
            $('textarea.form-control').on('input', function (e) {
                this.style.height = '200px';
                this.style.height = (this.scrollHeight + 6) + 'px';
            });

            $(document).on('click', '#submit-button', function () {
                let message = $('#message').val()
                let promo = $('#promo option:selected').val()
                let token = $('[name="_token"]').val()
                let publishDate = $('#publishDate').val()
                $.ajax({
                    type: 'POST',
                    url: "/post",
                    data: {message: message, promo: promo, publishDate: publishDate},
                    beforeSend: function () {
                        $('#cover-spin').show(0)
                    },
                    error: function (msg) {
                        $.each(msg.responseJSON.errors, function (k, v) {
                            toastr['warning'](k + ': ' + v)
                        })
                    },
                    success: function () {
                        toastr['success']('Пост успешно опубликован')
                        $(':input').val('')
                        $("#promo").val($("#promo option:first").val())
                    },
                    complete: function () {
                        $('#cover-spin').hide(0)
                    }
                })
            })

        })
    </script>
@endsection
