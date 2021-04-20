<div class="modal fade text-dark" id="promoInfo" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header p-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="promo-data">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="promo_name">Название</span>
                        <input type="text" class="form-control bg-white" placeholder="Введите название..." aria-label="Username"
                               aria-describedby="promo-name" name="promo_name" readonly>
                    </div>
                    @include('layout.datepicker', [
                        'pickerId' => 'promoStart',
                        'pickerName' => 'Начало акции',
                        'pickerPlaceholder' => 'Введите дату начала',
                        'property' => 'readonly disabled'])
                    @include('layout.datepicker', [
                        'pickerId' => 'promoEnd',
                        'pickerName' => 'Конец акции',
                        'pickerPlaceholder' => 'Введите дату завершения',
                        'property' => 'readonly disabled'])
                    <div class="text-start">
                        <span>Пример поста</span>
                    </div>
                    <div id="promo-example" class="card text-start">
                        <div class="card-body">
                            <span id="promo-layout-text" class=""></span>
                            <div id="layout-img"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-danger" id="delete-promo">Удалить</button>
                <button type="button" class="btn btn-primary" id="edit-promo">Редактировать</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let promoModal = new bootstrap.Modal(document.getElementById('promoInfo'))

        $(document).on('click', '.promo-toggle', function () {
            let project = $('.project-header').attr('project')
            let promo = $(this).parent('div').attr('id')
            console.log(promo, project)
            $.ajax({
                url: 'promo/' + promo + '/' + project,
                data: {promo: promo},
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('.promo-data input[name=promo_name]').prop('value', data.promo.name)
                    $('#promoStart').prop('value', data.start)
                    $('#promoEnd').prop('value', data.end)
                    $('#promo-layout-text').text(data.text)
                    $('#promoInfo').attr('value', promo)
                    if (data.img != null) {
                        $('#layout-img').html(
                            '<img src="' + data.img + '" style="object-fit: contain; height: 100%; width: 100%">'
                        )
                    }
                }
            })
            promoModal.toggle()
        })

        $('#edit-promo').click(function () {
            let promo = $('#promoInfo').attr('value')
            window.location.replace('/promo/edit/' + promo)
        })

        $(document).on('click', '#delete-promo',function () {
            let project = $('.project-header').attr('project')
            let promo = $('#promoInfo').attr('value')
            console.log(promo, project)
            $.ajax({
                method: 'POST',
                url: '/projects/remove_promo',
                data: {promo: promo, project: project},
                error: function (e) {
                    console.log(e)
                },
                success: function () {
                    $('button.project[data=' + project + ']').click()
                    promoModal.hide()
                    $('label[for=' + project + ']').click()
                }
            })
        })
    })
</script>
