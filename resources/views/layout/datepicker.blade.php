<div class="input-group mb-3">
    <label for="{{ $pickerId }}" class="input-group-text">{{ $pickerName }}</label>
    <input type="text" id="{{ $pickerId }}" name="{{ $pickerId }}" class="datepicker-here form-control bg-white"
           placeholder="{{ $pickerPlaceholder }}..." style="z-index: 1051" autocomplete="off" data-timepicker="true">
</div>

<script>
    $(document).ready(function () {
        $('#{{ $pickerId }}').datepicker({
            minDate: new Date(),
            minHours: new Date('H'),
            minMinutes: new Date('i')
        })

        $('#{{ $pickerId }}').inputmask("99.99.9999 99:99");
    })
</script>
