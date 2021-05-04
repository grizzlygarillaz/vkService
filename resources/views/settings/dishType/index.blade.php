@extends('layout.sidenav')
@section('content')
    <div class="content">
        <div class="input-group mb-3 w-lg-50">
            <input type="text" class="form-control" name="dish_type" id="dish_type" placeholder="Введите название категории" aria-describedby="add_dish_type">
            <button class="btn btn-success" type="button" id="add_dish_type">Добавить категорию</button>
        </div>
        <hr class="text-dark m-2">

        <div class="dish_type_list">
            @include('settings.dishType.list')
        </div>
    </div>

    <script>
        $('#add_dish_type').click(function () {
            $.ajax({
                url: '/settings/dish_type/add',
                method: 'post',
                data: {dish_type: $('#dish_type').val()},
                success: function (data) {
                    $('#dish_type').val('')
                    $('.dish_type_list').html(data)
                }
            })
        })

        function handleInput() {
            var text = $textarea.val();
            var highlightedText = applyHighlights(text);
            $highlights.html(highlightedText);
        }
        function applyHighlights(text) {
            return text
                .replace(/\n$/g, '\n\n')
                .replace(/[A-Z].*?\b/g, '<mark></mark>');
        }
        function handleScroll() {
            var scrollTop = $('textarea').scrollTop();
            $('.backdrop').scrollTop(scrollTop);
        }

        $('textarea').on({
            'input': handleInput,
            'scroll': handleScroll
        });
    </script>
@endsection
