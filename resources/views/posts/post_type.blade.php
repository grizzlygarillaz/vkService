<div class="input-group">
    <label class="input-group-text" for="select-object">{{$typeName}}</label>
    <select class="form-select select-object" id="select-object" name="{{ $type }}">
        <option value="" disabled {{ $selected ? '' : 'selected' }}>Выберите...</option>
        @if($objects)
        @foreach($objects as $object)
            <option value="{{ $object->id }}" {!!isset($object->sostav) ? "title=\"{$object->sostav}\"" : ''!!} {{ $selected == $object->id ? 'selected' : '' }}>{{ $object->name }}{{ isset($object->price) ? " / $object->price р." : '' }}</option>
        @endforeach
        @else
            <option disabled>Пусто</option>
        @endif
    </select>
</div>
