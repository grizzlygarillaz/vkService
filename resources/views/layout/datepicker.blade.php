<div class="input-group mb-3">
    <label for="{{ $pickerId }}" class="input-group-text">{{ $pickerName }}</label>
    <input type="text" id="{{ $pickerId }}" name="publishDate" {{ isset($property) ? $property : '' }} class="datepicker-here form-control bg-white"
           placeholder="{{ $pickerPlaceholder }}..." style="z-index: 1051" autocomplete="off"
           value="{{ isset($value) ? $value : '' }}">
</div>

<script>
    $('#{{ $pickerId }}').datepicker({
        timepicker: false,
        minDate: new Date()
    })

    $('#{{ $pickerId }}').inputmask("99.99.9999 99:99");
</script>
