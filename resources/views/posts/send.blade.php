@extends('layout.sidenav')
@section('content')
    <div class="mt-3">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div>
            @csrf
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="promo">Акция</label>
                </div>
                <select class="custom-select" id="promo" name="promo">
                    <option name="promo" selected>Выберите акцию...</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->name }}</option>
                    @endforeach
                </select>
            </div>
            <p class="font-weight-bolder">Введите текст поста</p>
            <div class="input-group">
                <textarea class="form-control" style="height: 200px" name="message"
                          aria-label="With textarea" id="message"></textarea>
                <div class="input-group-append">
                    <table class="table table-borderless input-group-text">
                        @if(isset($tags))
                            @foreach($tags as $tag)
                                <tr>
                                    <th scope="col" class="p-1 text-left tags" data="{{ $tag->tag }}">
                                        <button type="button" class="btn btn-sm btn-secondary p-1">{{ $tag->tag}}</button>
                                        - {{ $tag->description }}</th>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
            <button type="submit" id="submit-button" class="btn btn-primary mt-2">Submit</button>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.tags', function () {
                let textarea = $('[name = "message"]').val()
                $('[name = "message"]').val(textarea + $(this).attr('data'))
            })

            $('textarea.form-control').on('input', function(e) {
                this.style.height = '200px';
                this.style.height = (this.scrollHeight + 6) + 'px';
            });

            $(document).on('click', '#submit-button', function () {
                let message = $('#message').val()
                let promo = $('#promo option:selected').val()
                let token = $('[name="_token"]').val()
                $.ajax({
                    type: 'POST',
                    url: "/post/send",
                    data: {_token: token,message: message,promo: promo},
                    error: function (msg) {
                        console.log(msg)
                    }
                })
                location.reload()
            })
        })
    </script>
@endsection

