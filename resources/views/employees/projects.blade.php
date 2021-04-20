<div class="modal fade" tabindex="-1">
    <form action="/employees/projects/{{$employee}}" class="modal-dialog" method="post">
        <div class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Доступные проекты</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-auto" style="height: 400px">
                <input class="form-control w-75" type="text" id="project-search-employee" placeholder="Поиск...">
                <hr class="m-2 w-75">
                @foreach($projects as $project)
                    <div class="input-group w-75 mb-3 project-list">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" name="projects[]"
                                   type="checkbox" value="{{$project->id}}" id="{{$project->id}}" {{ empty($employeeProjects) ? '' : (in_array($project->id, $employeeProjects) ? 'checked' : '') }} aria-describedby="addon-wrapping">
                        </div>
                        <label class="form-control bg-white" for="{{$project->id}}">{{$project->name}}</label>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Выйти</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </form>
</div>
<script>
    $('#project-search-employee').keyup(function () {
        $('.project-list').each(function () {
            let str = $(this).text().toLowerCase()
            let search = $('#project-search-employee').val().toLowerCase()
            if (~str.indexOf(search)) {
                $(this).show()
            } else {
                $(this).hide()
            }
        })
    })
</script>
