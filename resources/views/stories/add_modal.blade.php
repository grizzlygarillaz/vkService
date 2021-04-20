<div class="modal fade" id="add-cp-story" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="/content_plan/add_story">
            @csrf
            <div class="modal-body">
                <input type="text" name="cp" value="" class="cp-identifier" hidden>
                @include('layout.datepicker', [
                'pickerId' => 'publishDateStory',
                'pickerName' => 'Дата',
                'pickerPlaceholder' => 'Введите дату публикации'])
                <div class="input-group mb-3">
                    <label class="input-group-text" for="story-type">Тип истории</label>
                    <select class="form-select" name="storyType" id="story-type">
                        <option selected value="">Контентный</option>
                        @foreach($postType as $key => $value)
                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-start mb-1">Контент по-умолчанию:</p>
                <div class="tab-content" id="nav-tabContent">
                    <div class="input-group">
                        <input type="text" name="story_link" class="form-control" placeholder="Вставьте ссылку">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary" id="success-story">Добавить</button>
            </div>
        </form>
    </div>
</div>
