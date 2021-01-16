@if(!empty($tags))
    <p class="text-muted m-0 mb-1">Теги автозамены:</p>
    <div class="container p-0 m-0">
        <div class="row row-cols-1 row-cols-md-auto">
            @foreach($tags as $tag)
                <div class="col m-1 tags input-group d-flex" style="width: auto !important;"
                     data="{{ $tag->tag}}">
                    <button type="button"
                            class="btn btn-sm btn-secondary p-1">{{ $tag->tag}}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            style="font-weight: 400; font-family: 'Roboto', sans-serif">{{ $tag->description }}</button>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.tags', function () {
                $('#{{ $textarea }}').insertAtCaret($(this).attr('data'));
            })
        })
    </script>
@endif
