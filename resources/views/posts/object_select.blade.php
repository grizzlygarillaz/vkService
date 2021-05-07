<option value="" disabled {{ $selected ? '' : 'selected' }}>Выберите...</option>
@if($objects)
    @foreach($objects as $object)
        <option value="{{ $object->id }}" {!!isset($object->sostav) ? "title=\"{$object->sostav}\"" : ''!!} {{ $selected == $object->id ? 'selected' : '' }}>{{ $object->name }}{{ isset($object->price) ? " / $object->price р." : '' }}</option>
    @endforeach
@else
    <option disabled>Пусто</option>
@endif
