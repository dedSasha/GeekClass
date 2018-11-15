@extends('layouts.app')

@section('title')
    GeekClass: "{{$thread->name}}"
@endsection

@section('tabs')

@endsection


@section('content')
    <h2 style="margin: 20px;"><a class="back-link" href="{{url('/insider/forum/')}}"><i
                    class="icon ion-chevron-left"></i></a>&nbsp;{{$thread->name}}</h2>

    @foreach($thread->orderedPosts() as $post)
        <div class="card">
            <div class="card-body @if (!$post->is_question and $post->getVotes()>=3) alert-success @endif">
                <div class="row">
                    <div style="float: left; width: 50px; padding: 5px;" class="col">
                        @if ($post->user->id != $user->id and $post->checkVote($user))
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center; line-height: 15px;">
                                <a title="Проголосовать за!"
                                   href="{{url('/insider/forum/'.$thread->id.'/upvote/'.$post->id)}}"
                                   onclick="return confirm('Вы уверены?');"><i
                                            class="icon ion-chevron-up"></i></a>
                            </div>
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center;">
                                {{$post->getVotes()}}
                            </div>
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center; line-height: 15px;">
                                <a title="Проголосовать против!"
                                   href="{{url('/insider/forum/'.$thread->id.'/downvote/'.$post->id)}}"
                                   onclick="return confirm('Вы уверены?');"><i
                                            class="icon ion-chevron-down"></i></a>
                            </div>

                        @else
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center; line-height: 15px;">
                                <a class="text-muted" title="Вы уже голосовали" href="#"
                                   onclick="return confirm('Вы уверены?');"
                                   style="@if($post->user->id != $user->id and $post->goodVote($user))color: green !important;@endif"><i
                                            class="icon ion-chevron-up"></i></a>
                            </div>
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center;">
                                {{$post->getVotes()}}
                            </div>
                            <div style="width: 100%; font-size: 30px; font-weight: 300; text-align: center; line-height: 15px;">
                                <a class="text-muted" title="Вы уже голосовали" href="#"
                                   onclick="return confirm('Вы уверены?');"
                                   style="@if($post->user->id != $user->id and !$post->goodVote($user))color: red !important;;@endif"><i
                                            class="icon ion-chevron-down"></i></a>
                            </div>
                        @endif

                    </div>

                    <div style="float: left; width: calc(100% - 50px); padding-left: 5px;" class="col-auto">
                        <div class="row">
                            <div class="col col-md-9">
                                @if ($post->user->image)
                                    <img src="{{url('/media/'.$post->user->image)}}"
                                         style="width: 50px; margin: 0;margin-right: 10px;"
                                         class="img-thumbnail" align="left">
                                @else

                                    <img src="https://api.adorable.io/avatars/250/{{$post->user->id}}.png"
                                         style="width: 50px; margin: 0;margin-right: 10px;"
                                         class="img-thumbnail" align="left">
                                @endif
                                <strong>{{$post->user->name}}</strong><br>
                                <span class="badge badge-pill badge-success"><i
                                            class="icon ion-ios-arrow-up"></i> {{$post->user->rank()->name}}</span>

                                @if ($post->user->is_trainee)
                                    <span class="badge badge-pill badge-info">Стажер</span>
                                @endif
                                @if ($post->user->is_teacher)
                                    <span class="badge badge-pill badge-info">Преподаватель</span>
                                @endif
                            </div>
                            <div class="col col-md-3">
                        <span class="float-right lead">
                            @if ($post->user == $user || $user->role=='teacher')
                                <a href="{{url('/insider/forum/'.$thread->id.'/edit/'.$post->id)}}"
                                   class="btn btn-sm btn-success"
                                   style="margin-right: 5px;margin-left: 5px;"><i class="icon ion-edit"></i></a>
                                @if (!$post->is_question)
                                    <a href="{{url('/insider/forum/'.$thread->id.'/delete/'.$post->id)}}"
                                       onclick="return confirm('Вы уверены?')" class="btn btn-sm btn-danger"
                                       style="margin-right: 5px;"><i class="icon ion-close-round"></i></a>
                                @endif
                            @endif
                       </span>
                            </div>
                        </div>

                        <div class="@if (!$post->is_question and $post->getVotes()<-2)text-muted @endif"
                             style="margin-top: 15px;">
                            @parsedown($post->text)
                        </div>
                        @if ($post->is_question)
                            @foreach($post->thread->tags as $tag)
                                <span class="badge badge-secondary badge-light"><a target="_blank"
                                            href="{{url('/insider/forum?tag='.$tag->name)}}">{{$tag->name}}</a></span>
                            @endforeach
                        @endif

                        <hr>
                        @foreach($post->comments as $comment)
                            <p style="font-size: 90%;"><strong>{{$comment->user->name}}:</strong> {{$comment->text}}</p>
                        @endforeach


                        <form action="{{url('/insider/forum/'.$thread->id.'/comment/'.$post->id)}}" method="post"
                              style="margin-bottom: 0px;">
                            {{csrf_field()}}
                            <div class="form-group" style="margin-bottom: 0px; font-size: 90%;">
                                <label for="comment{{$post->id}}">Новый комментарий:</label>
                                <textarea class="form-control" id="comment{{$post->id}}" rows="2"
                                          name="text"></textarea>
                                <input type="submit" value="Уточнить" class="btn btn-info btn-sm"
                                       style="margin-top: 10px;"/>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endforeach

    <div style="margin-top: 15px;">
        <h5 class="card-title">Ответить:</h5>

        <p class="text-muted">Опишите ваш ответ максимально точно и подробно, по возможности, приведите примеры кода.
            Для разметки текста используется markdown, описание - <a target="_blank"
                                                                     href="https://simplemde.com/markdown-guide">тут</a>.
        </p>

        <form action="{{url('/insider/forum/'.$thread->id.'/answer')}}" method="post">
            {{csrf_field()}}
            <div class="form-group" style="margin-bottom: 5px;">
                <textarea class="form-control" id="answer" name="text">{{old('text')}}</textarea>
                @if ($errors->has('text'))
                    <span class="help-block error-block">
                                        <strong>{{ $errors->first('text') }}</strong>
                                    </span>
                @endif
            </div>

            <div class="form-group">
                <p style="margin-top: 5px;">Перед отправкой еще раз проверьте правильность, точность и грамотность
                    вашего ответа. Кстати, если участники сочтут ответ хорошим и проголосуют за него более двух раз, вы
                    получите несколько GC.</p>

                <input type="submit" class="btn btn-success btn-lg" value="Отправить ответ"/>
            </div>
        </form>
    </div>

    <script>
        var simplemde_text = new EasyMDE({
            spellChecker: false,
            autosave: true,
            element: document.getElementById("answer")
        });
    </script>




@endsection
