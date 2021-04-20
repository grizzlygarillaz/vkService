<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header p-2">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h3>{{$currentObject['name']}}</h3>
            @foreach($currentObject['data'] as $value)
                @if(strlen($value['value']) > 55)
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{$value['key']}}</span>
                        <textarea class="form-control bg-white" readonly rows="3" aria-label="With textarea">{{$value['value']}}</textarea>
                    </div>
                @else
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="info{{$loop->index}}">{{$value['key']}}</span>
                        <input type="text" class="form-control bg-white" readonly aria-label="Username" value="{{$value['value']}}"
                               aria-describedby="info{{$loop->index}}">
                    </div>
                @endif
            @endforeach
            <hr>
            <h5>Изображения:</h5>
            @if(!empty($currentObject['image']))
                <div class="row-2">
                @foreach($currentObject['image'] as $value)
                    <img src="/{{$value['path']}}" class="col-5 rounded img-thumbnail" alt="...">
                @endforeach
                </div>
            @else
                <p>Пусто</p>
            @endif
        </div>
    </div>
</div>
