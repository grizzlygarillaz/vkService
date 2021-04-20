@foreach($tags as $tag)
    <div class="card mb-3 shadow">
        <div class="row g-0 p-3">
            <div class="col-md-4">
                <h5>Поле "{{$tag->field}}"</h5>
                <div class="input-group input-group-sm mb-3" title="Тег, который будет отображаться в панеле.">
                    <label for="tag:{{ $tag->object }}:{{ $tag->field }}" class="input-group-text">Тег:</label>
                    <input type="text" class="form-control" id="tag:{{ $tag->object }}:{{ $tag->field }}"
                           aria-describedby="basic-addon3" value="{{ $tag->tag ? $tag->tag : '' }}" placeholder="Введите тег без пробелов">
                </div>
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                <div class="input-group input-group-sm" title="Видимость в панеле тегов">
                    <label class="input-group-text bg-white overflow-hidden"
                           for="visible:{{ $tag->object }}:{{ $tag->field }}">Видимость</label>
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox"
                               value="visible" id="visible:{{ $tag->object }}:{{ $tag->field }}" {{ $tag->visible ? 'checked' : '' }}>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-md-8 d-flex">
                <div class="border m-3"></div>
                <div class="card-body p-0">
                    <h5 class="card-title">Описание</h5>
                    <textarea class="form-control w-100" placeholder="Опишите значение поля"
                              id="description:{{ $tag->object }}:{{ $tag->field }}" aria-label="With textarea">{{ $tag->description ? $tag->description : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endforeach
