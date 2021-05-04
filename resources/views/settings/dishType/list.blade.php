@foreach($dish_type_names as $name)
    <div class="card mb-3 shadow text-start p-2">
        <div class="card-body">
            <h5>Категория "{{$name->name}}"</h5>
            <h6>Фильтр блюд:</h6>
            <div class="backdrop">
                <div class="highlights">
                    <!-- cloned text with <mark> tags here -->
                </div>
            </div>
            <textarea class="form-control dish-filter" name="" id="" cols="30"
                      rows="3">@foreach($data[$name->name] as $filter){{$filter->filter . ' '}}@endforeach</textarea>
        </div>
    </div>
@endforeach

