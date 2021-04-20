@if(empty($objects))
    <p class="text-dark">Пусто</p>
@else
    @foreach($objects as $object)
        <div id="{{$object->id}}" class="input-group mb-3 {{$table}}-info">
            <span
                class="flex-grow-1 btn btn-outline-primary text-start {{$table}}-toggle">
                {{$object->name}} {{isset($object->ves) ? "| $object->ves " : ''}} {{isset($object->price) ? "| $object->price руб." : ''}}
            </span>
        </div>
    @endforeach
@endif
