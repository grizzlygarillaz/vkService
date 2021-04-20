<div id="project-story-content">
    <div class="project-data text-dark">
        @if (!isset($stories) or count($stories) == 0)
            <h4 class="text-start m-1 text-dark">Пусто</h4>
        @else
            @foreach($stories  as $story)
                <div class="card mb-4 cp-card" id="{{ $story['story']->id }}">
                    {{--                        @if($story['error'])--}}
                    {{--                            @foreach($story['error'] as $error)--}}
                    {{--                                <div class="card-header bg-danger text-white">--}}
                    {{--                                    <p class="p-0 m-0"><strong> {{ $error }} </strong></p>--}}
                    {{--                                </div>--}}
                    {{--                            @endforeach--}}
                    {{--                        @endif--}}
                    <div class="card-header align-items-center text-dark">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{date('d.m.Y H:i', (strtotime($story['story']->publish_date)))}}
                                - {{ $story['object'] }}</h5>
                            <div class="buttons">
                                <button class="btn btn-outline-primary story-settings me-3" id="">Редактировать</button>
                                <button class="btn btn-outline-danger story-delete" id="">Удалить из КП</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-between story-content">
                        @if(empty($story['image']))
                            <img class="border" src="/img/template_01.png" alt="" height="150">
                        @else
                            <img class="border" src="/{{$story['image']}}" alt="" height="150">
                        @endif
                    </div>

                    @if($story['users']['author'])
                        <div class="card-footer d-flex justify-content-between">
                                <span class="text-secondary">Автор:
                                    {{$story['users']['author']->name}}
                                    ({{ explode('@', $story['users']['author']->email)[0] }})
                                </span>
                            @if($story['users']['editor'])
                                <span class="text-secondary">
                                        Редактировал:
                                    {{$story['users']['editor']->name}}
                                    ({{ explode('@', $story['users']['editor']->email)[0] }})
                                    </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

<div class="modal fade modal-story-edit" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

