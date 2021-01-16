@if ($cp_post->count() == 0)
    <h4 class="text-start m-1 text-dark">Нет постов</h4>
@else
    @foreach($cp_post as $post)
        <div class="card m-1 cp-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">{{ date('d.m.Y H:i', (strtotime($post->publish_date)))}} - {{\Illuminate\Support\Facades\DB::Table('post_type')->find($post->post_type)->type}}</h5>
                <div class="buttons">
                    <button class="btn btn-outline-secondary me-3" id="post-settings">Редактировать</button>
                    <button class="btn btn-outline-danger" id="delete-post">Убрать из КП</button>
                </div>
            </div>
{{--            --}}
            @switch($post->post_type)
                @case(1)
                @include('posts.post_type.dish')
                @break
                @case(2)
                @include('posts.post_type.promo')
                @break
                @case(3)
                @include('posts.post_type.info')
                @break
            @endswitch
        </div>
    @endforeach
@endif
