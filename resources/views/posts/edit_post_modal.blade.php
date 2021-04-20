
    <div class="modal-dialog">
        <form class="modal-content" method="post" action='/content_plan/save/post/{{ $cpPostEdit->id }}'>
            @csrf
            <div class="modal-body">
                @include('layout.datepicker', [
                'pickerId' => 'publishEditPostDate',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации',
                'value' => date('d.m.Y H:i', (strtotime($cpPostEdit->publish_date)))])
                <div class="input-group mb-3">
                    <label class="input-group-text" for="post-type">Тип поста</label>
                    <select class="form-select" id="post-edit-type" name="postType">
                        <option {{ empty($cpPostEdit->post_type) ? 'selected' : '' }} value="">Контентный</option>
                        @foreach($postType as $key => $value)
                            <option {{ ($cpPostEdit->post_type == $key) ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="default-image">Контент по-умолчанию</span>
                    <input type="text" class="form-control" id="default_edit_image" name="image"
                           placeholder="Ссылка на изображение" value="{{$cpPostImage}}"
                           aria-label="Вставьте ссылку на изображение" aria-describedby="default-image">
                </div>

                <div class="d-flex justify-content-between">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Уведомление</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0"
                                   {{ $cpPostEdit->mute ? '' : 'checked' }} name="notification" type="checkbox"
                                   value="notify" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    @if(empty($cpPostEdit->post_type))
                        <div class="input-group mb-3" id="border-toggle">
                            <span class="input-group-text">Виньетка</span>
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" name="border" type="checkbox" value="border"
                                       aria-describedby="addon-wrapping" {{ $cpPostEdit->border ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endif
                </div>
                <span class="text-dark">Текст поста:</span>
                <div class="input-group">
                    <textarea class="form-control" id="post-edit-text" name='text' rows="5"
                              aria-label="With textarea">{{$cpPostEdit->text}}</textarea>
                </div>
                <div class="tag_list">
                    @include('layout.tags', ['textarea' => 'post-edit-text'])
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary" id="save-edit-post">Сохранить</button>
            </div>
        </form>
    </div>
<script>
    $(document).ready(function () {

        $('#post-edit-type').change(function () {
            $.ajax({
                url: '/content_plan/post/tags/' + $('option:selected', this).val(),
                data: {textarea: 'post-edit-text'},
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('.tag_list').html(data)
                }
            })
        })

        // $('#save-edit-post').click(function () {
        //     let cp = $('.cp-radio:checked').attr('id')
        //     let postType = $('#post-edit-type option:selected').attr('value')
        //     let text = $('#post-edit-text').val()
        //     let image = $('#default_edit_image').val()
        //     console.log(image)
        //     let postId = $('#postId').val()
        //     if ()
        //     let notify = $('')
        //     let publishDate = $('#publishDateEdit').val()
        //     $.ajax({
        //         method: 'POST',
        //         url: '/content_plan/save_post',
        //         data: {
        //             postType: postType,
        //             cp: cp,
        //             text: text,
        //             postId: postId,
        //             publishDate: publishDate,
        //             image: image
        //         },
        //         error: function (msg) {
        //             console.log(msg)
        //         },
        //         success: function (data) {
        //             $('#edit-cp-post').modal('hide')
        //             $('#cp-content').html(data)
        //         }
        //     })
        // })
    })
</script>
