<div class="modal fade text-dark" id="changePhotoPost{{$currentPost->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
     data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Отправится случайное изображение среди выбранных</h5>
            </div>
            <div class="modal-body row g-2">
                @if(isset($photos))
                    @if(count($photos) < 1)
                        <p>Нет изображений</p>
                    @endif
                    @foreach($photos as $photo)
                        <div class="col-4">
                            <div class="border rounded h-100 p-2 m-1 promo-photo">
                                <img src="/storage/{{ $photo->path }}"  id="{{$photo->id}}"
                                     class="box post-modal-image selected" alt="">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="input-group mb-3" hidden>
                <input type="file" class="form-control mx-3" id="inputGroupFile01">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="apply-photo-change"  data-bs-dismiss="modal">Изменить</button>
            </div>
        </div>
    </div>
</div>

