<div class="modal fade" id="add-cp-post" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="/content_plan/add_post">
            @csrf
            <div class="modal-body">
                <input type="text" name="cp" value="" class="cp-identifier" hidden >
                @include('layout.datepicker', [
                'pickerId' => 'publishAddPostDate',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации'])
                <div class="input-group mb-3">
                    <label class="input-group-text" for="post-type">Тип поста</label>
                    <select class="form-select" name="postType" id="post-type">
                        <option selected value="">Контентный</option>
                        @foreach($postType as $key => $value)
                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="default-image">Контент по-умолчанию</span>
                    <input type="text" class="form-control" id="default_image"
                           placeholder="Ссылка на изображение" name="image"
                           aria-label="Вставьте ссылку на изображение" aria-describedby="default-image">
                </div>
                <div class="d-flex justify-content-between">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Уведомление</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="notification" type="checkbox" value="notify"
                                   aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="input-group mb-3" id="border-toggle">
                        <span class="input-group-text">Виньетка</span>
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="border" type="checkbox" value="border"
                                   aria-describedby="addon-wrapping">
                        </div>
                    </div>
                </div>
                <span class="text-dark">Текст поста:</span>
                <div class="input-group">
                    <textarea class="form-control" id="post-text" rows="5" name="text"
                              aria-label="With textarea"></textarea>
                </div>
                <div class="tag_list">
                    @include('layout.tags', ['textarea' => 'post-text'])
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary" id="success-post">Добавить</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('#post-type').change(function () {
            $.ajax({
                url: '/content_plan/post/tags/' + $('option:selected', this).val(),
                data: {textarea: 'post-text'},
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('.tag_list').html(data)
                }
            })
            if (!$('option:selected', this).val()) {
                $('#border-toggle').removeClass('d-none')
            } else {
                $('#border-toggle').addClass('d-none')
            }

        })

        // $('#success-post').click(function () {
        //     let cp = $('.cp-radio:checked').attr('id')
        //     let postType = $('#post-type option:selected').attr('value')
        //     let text = $('#post-text').val()
        //     let image = $('#default_image').val()
        //     let publishDate = $('#publishDate').val()
        //     $.ajax({
        //         method: 'POST',
        //         url: '/content_plan/add_post',
        //         data: {postType: postType, cp: cp, text: text, publishDate: publishDate, image: image},
        //         error: function (msg) {
        //             console.log(msg)
        //         },
        //         success: function (data) {
        //             $('#add-cp-post').modal('hide')
        //             $('#cp-content').html(data)
        //         }
        //     })
        // })
    })
</script>
