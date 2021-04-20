<button class="btn btn-outline-success me-3" data-bs-toggle="modal" data-bs-target="#add-cp-poll">Добавить опрос
</button>

<div class="modal fade" id="add-cp-poll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="/content_plan/add_poll">
            @csrf
            <div class="modal-body">
                <input type="text" name="cp" value="" class="cp-identifier" hidden>
                @include('layout.datepicker', [
                'pickerId' => 'publishDate',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации'])
                <div class="input-group mb-3">
                    <label class="input-group-text" for="poll-type">Тип поста</label>
                    <select class="form-select" name="postType" id="poll-type">
                        <option selected value="">Контентный</option>
                        @foreach($postType as $key => $value)
                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Уведомление</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="notification" type="checkbox" value="notify"
                                   aria-describedby="addon-wrapping">
                        </div>
                    </div>
                </div>
                <span class="text-dark">Текст поста:</span>
                <div class="input-group">
                    <textarea class="form-control" id="poll-text" rows="3" name="text"
                              aria-label="With textarea"></textarea>
                </div>
                <div class="tag_list_poll">
                    @include('layout.tags', ['textarea' => 'poll-text'])
                </div>
                <hr class="text-secondary m-2 p-0">
                <div class="text-start poll-block">
                    <h5>Опрос:</h5>
                    <p class="mb-2">Фон:</p>
                    <div class="btn-group mb-3" role="group" aria-label="Basic radio toggle button group">
                        @foreach(\Illuminate\Support\Facades\DB::table('poll_backgrounds')->get() as $pollBG)
                            <input type="radio" {{$loop->first ? 'checked' : ''}} class="btn-check poll-bg" name="background_poll" id="poll-bg{{$pollBG->id}}"
                                   autocomplete="off" value="{{$pollBG->id}}">
                            <label class="btn btn-outline-secondary px-1 pt-1" for="poll-bg{{$pollBG->id}}"
                                   style="width: 50px; height: 50px">
                                <div class="border rounded d-flex w-100 h-100 bg-light" style="{!!$pollBG->style!!}"></div>
                            </label>
                        @endforeach
                        <input type="radio" class="btn-check poll-bg" name="background_poll" id="poll-bg"
                               autocomplete="off" value="photo">
                        <label class="btn btn-outline-secondary px-1 pt-1" for="poll-bg"
                               style="height: 50px">
                            <img src="/img/template_01.png" alt="" class="border rounded d-flex h-100 bg-light text-dark"></img>
                        </label>
                    </div>
                    <div class="input-group mb-3" style="display: none;">
                        <label class="input-group-text" id="default-image">Изображение</label>
                        <input type="text" disabled class="form-control" id="default_image"
                               placeholder="Ссылка на изображение" name="image"
                               aria-label="Вставьте ссылку на изображение" aria-describedby="default-image">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Анонимный опрос</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="anonymous" type="checkbox" value="anonymous"
                                   aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="poll-question" class="form-label">Текст опроса:</label>
                        <textarea class="form-control" id="poll-question" name="question" rows="2"></textarea>
                    </div>
                    <p class="mb-2">Ответы:</p>
                    <input type="text" class="form-control form-control-sm mb-2 poll-answer" placeholder="Введите ответ"
                           aria-label="Recipient's username" name='answer[]'>
                    <div class="input-group input-group-sm poll-answer-group mb-2">
                        <input type="text" class="form-control poll-answer" placeholder="Введите ответ"
                               aria-label="Recipient's username" name='answer[]'>
                        <button class="btn btn-outline-danger delete-answer" type="button">✕</button>
                    </div>

                    <button type="button" class="btn btn-outline-success btn-sm w-100 add-answer">
                        Добавить ответ
                    </button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary" id="success-poll">Добавить</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {

        $(document).on('click', '.add-answer', (function () {
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
        }))
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
        $('.poll-bg').change(function () {
            if ($('.poll-bg:checked').val() == 'photo') {
                $('#default_image').attr('disabled', false).parent('div').fadeIn('fast')
            } else {
                $('#default_image').attr('disabled', true).parent('div').fadeOut('fast')
            }
        })
    })
</script>
