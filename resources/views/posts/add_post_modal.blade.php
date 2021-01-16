<div class="modal fade" id="add-cp-post" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                @include('layout.datepicker', [
                'pickerId' => 'publishDate',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации'])
                <div class="input-group mb-3">
                    <label class="input-group-text" for="post-type">Тип поста</label>
                    <select class="form-select" id="post-type">
                        <option selected>Выберите тип</option>
                        @foreach($postType as $type)
                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Текст пост</span>
                    <textarea class="form-control" id="post-text" aria-label="With textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="success-post">Добавить</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        let addPostModal = new bootstrap.Modal(document.getElementById('add-cp-post'), {
            backdrop: 'static'
        })

        $(document).on('click', '#success-post', function () {
            let cp = $('.cp-radio:checked').attr('id')
            let postType = $('#post-type option:selected').attr('value')
            let promo = $('#post-promo option:selected').attr('value')
            let text = $('#post-text').val()
            let publishDate = $('#publishDate').val()
            console.log(postType, promo, text, cp)
            $.ajax({
                method: 'POST',
                url: '/content_plan/add_post',
                data: {postType: postType, cp: cp, _token: "{{ csrf_token() }}",
                    promo: promo, text: text, publishDate: publishDate},
                error: function (msg) {
                    console.log(msg)
                },
                success: function () {
                    addPostModal.hide()
                }
            })
        })
    })
</script>
