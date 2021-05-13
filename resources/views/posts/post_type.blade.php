<div class="input-group">
    <label class="input-group-text" for="select-object">{{$typeName}}</label>
    <select class="form-select select-object" id="select-object" name="{{ $type }}">
        <option value="" disabled {{ $selected ? '' : 'selected' }}>Выберите...</option>
        @if($type == 'dish')
            <option value="queue" {{ $selected == 'queue' ? 'selected' : ''}}>По очереди</option>
        @endif
        @if($objects)
            @foreach($objects as $object)
                <option
                    value="{{ $object->id }}"
                    {!!isset($object->sostav)
                        ? "title=\"{$object->sostav}\""
                        : ''!!} {{ $selected == $object->id ? 'selected' : '' }}
                    @if($type == 'dish' && $loop->last) style="background-color: #f3e5b5" @endif>
                    {{ $object->name }}{{ isset($object->price) ? " / $object->price р." : '' }}
                </option>
            @endforeach
        @else
            <option disabled>Пусто</option>
        @endif
    </select>
</div>
