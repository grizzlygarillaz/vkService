@foreach($dish_type_names as $name)
    <div class="card mb-3 shadow text-start p-2">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h5>Категория "{{$name->name}}"</h5>
                <button class="btn btn-outline-danger material-icons delete-category" id="{{$name->name}}">delete</button>
            </div>
            <h6>Фильтр блюд:</h6>
            <textarea class="form-control dish-filter" id="{{$name->name}}" cols="30"
                      rows="3">@foreach($data[$name->name] as $filter){{$filter->filter . ($loop->last ? '' : ',')}}@endforeach</textarea>
        </div>
    </div>
@endforeach

