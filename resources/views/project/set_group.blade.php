<h4 class="text-dark">У проекта не определена группа!</h4>
<form action="/projects/group/set" method="post">
    @csrf
    <input type="text" name="project" hidden value="{{ $project }}">
    <div class="input-group mb-3">
        <span class="input-group-text">Группа</span>
        <input type="text" placeholder="Введите ссылку на группу..." title="Пример: https://vk.com/public1"
               class="form-control bg-white" name="group">
    </div>
    <button class="btn btn-success" type="submit">Сохранить</button>
</form>
