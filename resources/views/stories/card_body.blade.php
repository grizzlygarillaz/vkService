<div class="card-header align-items-center text-dark"
     @if ($story['story']->to_publish)
     style="background: #fcf4ab"
    @endif>
    <div class="d-flex justify-content-between">
        <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($story['story']->publish_date)))}}
            - {{ $story['object'] }}</h5>
        <div class="buttons">
            <button class="btn btn-outline-primary story-settings" id="">Редактировать</button>
        </div>
    </div>
</div>
<div class="card-body d-flex justify-content-between story-content">
    @if(empty($story['image']))
        <img class="border" src="/img/template_01.png" alt="" height="150">
    @else
        <img class="border" src="/{{$story['image']}}" alt="" height="150">
    @endif
</div>
<div class="card-footer d-flex justify-content-between p-3">
    @if($story['story']->stories_type)
        @include("stories.story_type", [
                'objects' => isset($objects[$story['story']->stories_type]) ? $objects[$story['story']->stories_type] : null,
                'selected' => $story['story']->object_id,
                'typeName' => $story['object'],
                'type' => $story['story']->stories_type
            ])
    @else
        <p></p>
    @endif
    {{--                                {{ ($story['error']) ? 'disabled' : '' }}--}}
    @if ($story['story']->to_publish)
        <button class="btn btn-primary ml-4 unsend-story" >
            Отменить публикацию
        </button>
    @else
        <button class="btn btn-primary ml-4 send-story" >
            Опубликовать
        </button>
    @endif
</div>
