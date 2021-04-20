<div class="modal-dialog">
    <form class="modal-content text-dark" method="post" action="{{isset($cp) ? '/content_plan' : ''}}/stories/edit/{{$data['story']->id}}">
        @csrf
        <div class="modal-body">
            @include('layout.datepicker', [
            'pickerId' => 'publishDateStory',
            'pickerName' => 'Дата',
            'pickerPlaceholder' => 'Введите дату публикации',
            'value' => $data['date']])
            <p class="text-start mb-1">Контент:</p>
            <div class="tab-content" id="nav-tabContent">
                <div class="input-group">
                    <input type="text" name="story_link" class="form-control" value="{{$data['content']}}"
                           placeholder="Вставьте ссылку">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="submit" class="btn btn-primary" id="success-story">Сохранить</button>
        </div>
    </form>
</div>
