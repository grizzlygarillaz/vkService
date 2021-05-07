@extends('layout.sidenav')
@section('content')
    <div class="content text-start">
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

        $(document).on('focusout', '.dish-filter', function () {
            let filter = $(this).val()
            let category = $(this).attr('id')
            $.ajax({
                url: '/settings/dish_type/set_filter',
                method: 'post',
                data: {filter: filter, category: category}
            })
        })

        $(document).on('click', '.delete-category', function () {
            let category = $(this).attr('id')
            swal({
                title: "Вы уверены?",
                icon: "warning",
                buttons: ['Отмена', 'Да'],
                dangerMode: true,
            })
                .then((willSend) => {
                    if (willSend) {
                        $.ajax({
                            url: '/settings/dish_type/delete_filter/' + category,
                            success: function (html) {
                                $('.dish_type_list').html(html)
                            }
                        })
                    }
                })

        })
    </script>
@endsection
