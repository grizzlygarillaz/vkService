<div class="modal-dialog">
    <form class="modal-content" method="post" action="/posts/edit/{{ $post->id }}">
        <div class="modal-body">
            @csrf
            @include('layout.datepicker', [
            'pickerId' => 'publishEditPostDate',
            'pickerName' => 'Дата',
            'pickerPlaceholder' => 'Введите дату публикации',
            'value' => date('d.m.Y H:i', (strtotime($post->publish_date)))])
            <div class="input-group mb-3">
                <span class="input-group-text" id="default-image">Контент</span>
                <input type="text" class="form-control" id="default_edit_image" name="post_image"
                       placeholder="Ссылка на изображение" value="{{$postImage}}"
                       aria-label="Вставьте ссылку на изображение" aria-describedby="default-image">
            </div>
            <div class="d-flex justify-content-between">
                <div class="input-group mb-3">
                    <span class="input-group-text">Уведомление</span>
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" {{ $post->mute ? '' : 'checked' }} name="notification"
                               type="checkbox" value="notify" aria-describedby="addon-wrapping">
                    </div>
                </div>

                @if(empty($post->post_type))
                    <div class="input-group mb-3" id="border-toggle">
                        <span class="input-group-text">Виньетка</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="border" type="checkbox" value="border"
                                   aria-describedby="addon-wrapping" {{ $post->border ? 'checked' : '' }}>
                        </div>
                    </div>
                @endif
            </div>
            <span class="text-dark">Текст поста:</span>
            <div class="input-group">
                <textarea class="form-control" id="{{ $textarea }}" name="text" rows="5"
                          aria-label="With textarea">{{$post->text}}</textarea>
            </div>
            <div class="m-2 text-center">
                {!! $tags !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="submit" class="btn btn-primary" id="save-edit-post">Сохранить</button>
        </div>
    </form>
</div>
