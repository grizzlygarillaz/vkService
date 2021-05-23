<div class="input-group mb-2">
    <label class="input-group-text" for="select-object">Категория</label>
    <select class="form-select select-dish-category" name="dish_category">
        @if($categories)
            <option value="all" selected>Все</option>
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
            <option value="other">Другое</option>
        @else
            <option disabled>Пусто</option>
        @endif
    </select>
</div>
