<div class="input-group">
    <label class="input-group-text" for="select-object">{{$typeName}}</label>
    <select class="form-select select-object" id="select-object" name="{{ $type }}">
        <option value="" disabled {{ $selected ? '' : 'selected' }}>Выберите...</option>
        @if($objects)
            @foreach($objects as $object)
                <option value="{{ $object->id }}" {{ $selected == $object->id ? 'selected' : '' }}>{{ $object->name }}</option>
            @endforeach
        @else
            <option disabled>Пусто</option>
        @endif
    </select>
</div>

<script>
    $('.select-object').change(function () {
        let data = {}
        data['object'] = $('option:selected', this).val()
        data['story'] = $(this).closest('.cp-card').attr('id')
        data['type'] = $(this).attr('name')
        let id = $('.project-header').attr('project')
        $.ajax({
            url: '/projects/selectType/story/' + id,
            data: data,
            error: function (msg) {
                console.log(msg)
            },
            success: function (data) {
                console.log(data)
                $('.project-page input:checked').click()
            }
        })
    })
</script>
