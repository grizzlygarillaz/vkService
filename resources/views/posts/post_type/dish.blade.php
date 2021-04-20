<div class="input-group mb-3">
    <label class="input-group-text" for="select-object">{{$typeName}}</label>
    <select class="form-select" id="select-object" name="{{ $type }}">
        <option value="" disabled selected>Выберите {{mb_strtolower($typeName)}}</option>
        @foreach($objects as $object)
            <option value="{{ $object->id }}">{{ $object->name }}</option>
        @endforeach
    </select>
</div>

<script>
    $('#select-object').change(function () {
        
    })
</script>
