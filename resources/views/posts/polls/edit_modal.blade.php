<div class="modal-dialog">
    <form class="modal-content" method="post" action="/posts/edit/{{ $poll->id }}/{{ $editFrom }}">
        @csrf
        <div class="modal-body">
            @include('layout.datepicker', [
            'pickerId' => 'publishDate',
            'pickerName' => 'Дата',
            'pickerPlaceholder' => 'Введите дату публикации',
            'value' => date('d.m.Y H:i', (strtotime($poll->publish_date)))])
            <div class="input-group mb-3">
                <label class="input-group-text" for="poll-type">Тип поста</label>
                <select class="form-select" name="postType" id="poll-type">
                    <option {{ is_null($poll->post_type) ? 'selected' : '' }} value="">Контентный</option>
                    @foreach($postType as $key => $value)
                        <option
                            value="{{ $key }}" {{$poll->post_type == $key ? 'selected' : ''}}>{{ $value['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <div class="input-group mb-3">
                    <span class="input-group-text">Уведомление</span>
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" name="notification" type="checkbox" value="notify"
                               aria-describedby="addon-wrapping" {{$poll->mute ? '' : 'checked'}}>
                    </div>
                </div>
            </div>
            <span class="text-dark">Текст поста:</span>
            <div class="input-group">
                    <textarea class="form-control" id="poll-edit-text" rows="3" name="text"
                              aria-label="With textarea">{{$poll->text}}</textarea>
            </div>
            <div class="tag_list_poll">
                @include('layout.tags', ['textarea' => 'poll-edit-text'])
            </div>
            <hr class="text-secondary m-2 p-0">
            <div class="text-start poll-block">
                <h5>Опрос:</h5>
                <div class="btn-group mb-3" role="group" aria-label="Basic radio toggle button group">
                    @foreach(\Illuminate\Support\Facades\DB::table('poll_backgrounds')->get() as $pollBG)
                        <input type="radio"
                               {{key_exists('background', $pollJSON) ? ($pollBG->id == $pollJSON['background'] ? 'checked' : '') : ''}} class="btn-check poll-edit-bg"
                               name="background_poll" id="poll-edit-bg{{$pollBG->id}}"
                               autocomplete="off" value="{{$pollBG->id}}">
                        <label class="btn btn-outline-secondary px-1 pt-1" for="poll-edit-bg{{$pollBG->id}}"
                               style="width: 50px; height: 50px">
                            <div class="border rounded d-flex w-100 h-100 bg-light" style="{!!$pollBG->style!!}"></div>
                        </label>
                    @endforeach
                    <input type="radio"
                           {{key_exists('background', $pollJSON) ? '' : 'checked'}} class="btn-check poll-edit-bg"
                           name="background_poll" id="poll-edit-bg"
                           autocomplete="off" value="photo">
                    <label class="btn btn-outline-secondary px-1 pt-1" for="poll-edit-bg"
                           style="height: 50px">
                        <img src="/img/template_01.png" alt=""
                             class="border rounded d-flex h-100 bg-light text-dark"></img>
                    </label>
                </div>
                <div class="input-group mb-3" {{key_exists('background', $pollJSON) ? '' : 'style="display: none;"'}}>
                    <label class="input-group-text" id="default-image">Изображение</label>
                    <input type="text" {{key_exists('background', $pollJSON) ? 'disabled' : ''}} class="form-control" id="default_edit_image"
                           placeholder="Ссылка на изображение" name="image" value="{{$pollImage}}"
                           aria-label="Вставьте ссылку на изображение" aria-describedby="default-image">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Анонимный опрос</span>
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" name="anonymous" type="checkbox" value="anonymous"
                               aria-describedby="addon-wrapping" {{$pollJSON['anonymous'] ? 'checked' : ''}}>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="poll-question" class="form-label">Текст опроса:</label>
                    <textarea class="form-control" id="poll-question" name="question"
                              rows="2">{{$pollJSON['question']}}</textarea>
                </div>
                <p class="mb-2">Ответы:</p>
                @foreach($pollJSON['answer'] as $key => $answer)
                    @if($key == 0)
                        <input type="text" class="form-control form-control-sm mb-2 poll-answer"
                               placeholder="Введите ответ"
                               aria-label="Recipient's username" name='answer[]' value="{{$answer}}">
                    @else
                        <div class="input-group input-group-sm poll-answer-group mb-2">
                            <input type="text" class="form-control poll-answer" placeholder="Введите ответ"
                                   aria-label="Recipient's username" name='answer[]' value="{{$answer}}">
                            <button class="btn btn-outline-danger delete-answer" type="button">✕</button>
                        </div>
                    @endif
                @endforeach
                <button type="button" class="btn btn-outline-success btn-sm w-100 add-answer">
                    Добавить ответ
                </button>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="submit" class="btn btn-primary" id="success-poll">Сохранить</button>
        </div>
    </form>
</div>

<script>
    $('.poll-edit-bg').change(function () {
        if ($('.poll-edit-bg:checked').val() == 'photo') {
            $('#default_edit_image').attr('disabled', false).parent('div').fadeIn('fast')
        } else {
            $('#default_edit_image').attr('disabled', true).parent('div').fadeOut('fast')
        }
    })
</script>
