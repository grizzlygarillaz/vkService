<div class="card-body d-flex p-0" id="{{ $post->id }}">
    <div class="form-floating w-100 ml-4 my-2">
        <textarea class="form-control h-100 bg-white" readonly placeholder="Введите текст поста..."
                  id="post-text-area-{{$post->id}}" style="height: 100px">{{ $postText[$post->id] }}</textarea>
        <label for="post-text-area">Текст поста</label>
    </div>
    </p>
    <div id='img-container'>
        <img src="/img/template_01.png" class="border-0 post-img mt-2" alt="">
        <button class="btn btn-outline-dark m-2 w-auto" id="change-post-photo">Изменить изображение</button>
    </div>
</div>
<div class="card-footer ">
    <div class="d-flex justify-content-between align-items-center mb-2" data="post-promo">
        <div class="input-group me-3" id="cp-type">
            <label class="input-group-text" for="promo-choose">Акция</label>
            <select class="form-select" id="promo-choose">
                <option selected value="default">Выберите акцию</option>
                @if ($promos !== null)
                    @foreach($promos as $promo)
                        <option value="{{$promo->id}}">{{$promo->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-check form-switch">
            <input class="form-check-input promo-layout-toggle" type="checkbox" id="text-{{$post->id}}">
            <label class="form-check-label text-nowrap" for="text-{{$post->id}}">Шаблон акции</label>
        </div>
    </div>
    <div class="d-flex justify-content-between">
        <input type="text" class="form-control" placeholder="Комментарий к посту" id="posts-comment"
               aria-label="Username" aria-describedby="addon-wrapping">
        <button class="btn btn-success ms-3" id="send-to-publish">Опубликовать</button>
    </div>
</div>

<div class="modal fade text-dark" id="changePhotoPost{{ $post->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
     data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Отправится случайное изображение среди выбранных</h5>
            </div>
            <div class="modal-body row g-2">
                @if(isset($photos))
                    @if(count($photos) < 1 || is_null($photos))
                        <p>Нет изображений</p>
                    @endif
                    @foreach($photos as $photo)
                        <div class="col-4">
                            <div class="border rounded h-100 p-2 m-1 promo-photo">
                                <img src="/storage/{{ $photo->path }}" id="{{$photo->id}}"
                                     class="box post-modal-image selected" alt="">
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>Нет изображений</p>
                @endif
            </div>
            <div class="input-group mb-3" hidden>
                <input type="file" class="form-control mx-3" id="inputGroupFile01">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="apply-photo-change" data-bs-dismiss="modal">Изменить
                </button>
            </div>
        </div>
    </div>
</div>

