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
        <div class="card-body d-flex p-0">
            <p class="overflow-hidden text-start cp-description m-3" style="height: 150px">
                {{ $post->text }}
            </p>
            <div id='img-container'>
                <img src="/storage/Promo test 2/wCZjAWwotC1r0g8jwbGjmQxXj.jpg" class="border-0 post-img my-2" alt="">
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div class="input-group w-50 me-3" id="cp-type">
                <label class="input-group-text" for="type-choose">Тип</label>
                <select class="form-select" id="type-choose">
                    <option selected>Choose...</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            <input type="text" class="form-control" placeholder="Комментарий к посту" id="posts-comment"
                   aria-label="Username" aria-describedby="addon-wrapping">
        </div>
    </div>
    @endforeach
@endif
