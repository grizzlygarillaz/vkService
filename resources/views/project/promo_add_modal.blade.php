<div class="modal fade text-dark" id="addPromo" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @csrf
            <div class="modal-body">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button data="locked"  class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#promo-locked" aria-expanded="true" aria-controls="promo-locked">
                                Общие акции
                            </button>
                        </h2>
                        <div id="promo-locked" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" data="private" type="button" data-bs-toggle="collapse" data-bs-target="#promo-private" aria-expanded="false" aria-controls="promo-private">
                                Персональные акции
                            </button>
                        </h2>
                        <div id="promo-private" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="add-promo">Добавить</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let addPromoModal = new bootstrap.Modal(document.getElementById('addPromo'), {
            backdrop: 'static'
        })
        $(document).on('click', '#add-promo', function () {
            let project = $('.project-header').attr('project')
            let promo = {}
            let count = 0;
            $('#promo-locked .accordion-body input').each(function () {
                if ($(this).prop('checked') == true) {
                    promo[count] = $(this).val()
                    count++
                }
            })
            $.ajax({
                method: 'POST',
                url: '/projects/add_promo',
                data: {project: project, promos: promo, _token: '{{csrf_token()}}'},
                statusCode: {
                    function(e) {
                        console.log(e)
                    }
                },
                success: function () {
                    let project = $('.project-header').attr('project')
                    addPromoModal.hide()
                    $('label[for=' + project + ']').click()
                }
            })
        })

        $(document).on('click', 'button.add-promo', function () {
            let project = $('.project-header').attr('project')
            $.ajax({
                url: '/promo/for_project',
                data: {id: project},
                dataType: 'json',
                error: function (msg) {
                    console.log(msg)
                },
                success: function (data) {
                    $('#promo-locked .accordion-body').html(data.available_promo)
                }
            })
            addPromoModal.toggle()
        })
    })
</script>
