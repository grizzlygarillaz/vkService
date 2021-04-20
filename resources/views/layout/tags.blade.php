<?php
$tags = \Illuminate\Support\Facades\DB::table('tag_list')->get();
?>

@if(!empty($tags))
    <div class="mt-2"></div>
    <button type="button" class="emojipicker">
        Emoji
    </button>
    <button class="btn btn-secondary btn-sm m-0 mb-1 tag-toggle-{{ $textarea }}" type="button">Теги автозамены:
    </button>
    <div class="container p-0 m-0 tag-container">
        <div class="row row-cols-1 row-cols-md-auto">
            @foreach($tags as $tag)
                <div class="col m-1 tags-{{ $textarea }} input-group d-flex p-0" style="width: auto !important;"
                     data="{{ $tag->tag}}">
                    <button type="button" class="btn py-0 btn-sm btn-outline-secondary"
                            style="font-weight: 400; font-family: 'Roboto', sans-serif">{{$tag->tag}}</button>
                </div>
            @endforeach
            @if( isset($objTags) )
                @foreach( $objTags as $objTag )
                    <div class="col m-1 tags-{{ $textarea }} input-group d-flex p-0" style="width: auto !important;"
                         data="::{{$objTag->tag}}::" title="{{$objTag->description}}">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                style="font-weight: 400; font-family: 'Roboto', sans-serif">::{{$objTag->tag}}::
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <script>
        $(document).ready(function () {
            new FgEmojiPicker({
                trigger: ['.emojipicker'],
                position: ['bottom', 'right'],
                dir: '/js/',
                emit(emoji, triggerElement) {
                    $('#{{ $textarea }}').insertAtCaret(emoji.emoji);
                }
            })
            $('.tags-{{ $textarea }}').click(function () {
                $('#{{ $textarea }}').insertAtCaret(' ' + $(this).attr('data'));
            })
            $('.tag-toggle-{{ $textarea }}').click(function () {
                $(this).siblings('.tag-container').first().fadeToggle("fast", "linear")
            })
        })
    </script>
@endif
