<div class="input-group mb-2">
    <label class="input-group-text" for="select-object">Категория</label>
    <select class="form-select select-dish-category" name="dish_category">
        <option value="all" selected>Все</option>
        @if($categories)
            @foreach($categories as $category)
                <option value="{{ $category->name }}">{{ $category->name }}</option>
            @endforeach
            <option value="other">Другое</option>
        @else
            <option disabled>Пусто</option>
        @endif
    </select>
</div>
