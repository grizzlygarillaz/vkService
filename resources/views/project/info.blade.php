<div id="project-about-content">
    <h4 class="text-dark">Контент план:</h4>
    <select name="content_plan" id="project_content_plan" class="form-select">
        <option value="default" {{empty($cpProject) ? 'selected' : ''}} disabled>Выберите КП</option>
        @foreach($cpList as $cp)
            <option value="{{$cp->id}}" {{$cp->id == $cpProject ? 'selected' : ''}}>{{$cp->name}}</option>
        @endforeach
    </select>
    <hr class="text-dark mb-3">

    <h4 class="text-dark">Ссылка для клиентов:</h4>
    <div class="input-group mb-3">
        <input type="text" class="form-control bg-light" value="{{$link}}" readonly placeholder="Нет ссылки" id="project_link" aria-label="Нет ссылки" aria-describedby="proj_link">
        <button class="btn btn-outline-secondary" title="Обновить ссылку" type="button" id="proj_link">Обновить</button>
    </div>
    <hr class="text-dark mb-3">

    <h4 class="text-dark">Данные проекта:</h4>
    @foreach($info as $key => $value)
        <div class="input-group mb-3 editable-info">
            <span class="input-group-text">{{ $value['name'] }}:</span>
            <input type="text" readonly placeholder='{{ key_exists('rule', $value) ? $value['rule'] : "Нет данных..." }}'
                   class="form-control bg-white" id="{{ $key }}" value='{{ $value['value']}}'
                   title="{{ key_exists('example', $value) ? $value['example'] : "" }}" name="{{ $key }}"
            >
        </div>
    @endforeach
    <hr class="text-dark mb-3">
    <h4 class="text-dark">Теги:</h4>
    <div class="project-data">
        @foreach($data as $key => $value)
            <div class="input-group mb-3">
                <span class="input-group-text">{{ $key }}:</span>
                <input type="text" placeholder="Нет данных..."
                       class="form-control bg-white" {!! empty($value) ? 'style="background: lightpink !important"' : ''!!} readonly value='{{ $value }}'>
            </div>
        @endforeach
    </div>
</div>

<script>
    $('#project_content_plan').change(function () {
        let project = $('.project-header').attr('project')
        let cp = $('option:selected', this).val()
        $.ajax({
            url: '/projects/info/content_plan/' + project,
            data: {cp: cp},
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function () {
            }
        })
    })

    $('#proj_link').click(function () {
        let project = $('.project-header').attr('project')
        $.ajax({
            url: '/projects/create/token/' + project,
            error: function (msg) {
                swal('Ошибка!', msg.responseJSON.message, "error")
            },
            success: function (data) {
                $('#project_link').val(data)
            }
        })
    })
</script>
