@extends('layouts.app')

@section('title')
    GeekClass: "{{$step->course->name}}" - "{{$step->name}}"
@endsection

@section('tabs')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">

            <h2><span style="font-weight: 200;"><a style="display: inline;" class="nav-link" role="tab" id="back-link"
                                                   href="{{url('/insider/courses/'.$step->course_id)}}"><i
                                class="icon ion-chevron-left"></i></a>{{$step->course->name}} - </span>{{$step->lesson->name}}</h2>
        </div>
        @if ($user->role=='teacher')
            <div class="col-md-4">

                <a href="{{url('/insider/steps/'.$step->id.'/edit')}}"
                   class="float-right btn btn-sm btn-success">Редактировать</a>
                <button style="margin-right: 5px;" type="button" class="float-right btn btn-sm btn-primary"
                        data-toggle="modal" data-target="#exampleModal">
                    Добавить задачу
                </button>
            </div>
        @endif
    </div>

    <div class="row" style="margin-top: 15px;">
        <div class="col-md-3" >
            <div class="list-group">
                @foreach($step->lesson->steps as $lesson_step)
                    <a href="{{url('/insider/steps/'.$lesson_step->id)}}" style="line-height: 1.5; padding: 7px 16px;" class="list-group-item @if ($lesson_step->id==$step->id) active @else list-group-item-action @endif">{{$lesson_step->name}}</a>
                @endforeach
            </div>

            @if ($user->role=='teacher')
                <p align="center" style="margin-top: 15px;">
                    <a href="{{url('/insider/lessons/'.$step->lesson->id.'/create')}}" class="btn btn-success btn-sm">Новый
                        этап</a>
                </p>
            @endif
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="theory-tab" data-toggle="pill" href="#theory" role="tab"
                               aria-controls="theory" aria-expanded="true">0. Теория</a>
                        </li>
                        @foreach ($tasks as $key => $task)
                            <li class="nav-item">
                                <a class="nav-link" id="tasks-tab{{$task->id}}" data-toggle="pill"
                                   href="#task{{$task->id}}"
                                   role="tab"
                                   aria-controls="tasks{{$task->id}}" aria-expanded="true">{{$key+1}}. {{$task->name}}
                                    @if($task->is_star) <sup>*</sup> @endif
                                    @if($task->only_class) <sup><i class="icon ion-android-contacts"></i></sup> @endif
                                    @if($task->only_remote) <sup><i class="icon ion-at"></i></sup> @endif</a>
                            </li>
                        @endforeach

                    </ul>
                </div>


            </div>
            <div class="tab-content" id="pills-tabContent" style="margin-top: 15px; margin-bottom: 15px;">
                <div class="tab-pane fade show active" id="theory" role="tabpanel" aria-labelledby="v-theory-tab">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body markdown">
                                    @parsedown($step->theory)
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($user->role=='teacher' && $step->notes!='')
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body markdown">
                                        <h3>Комментарий для преподавателя</h3>
                                        @parsedown($step->notes)
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @foreach ($tasks as $key => $task)
                    <div class="tab-pane fade" id="task{{$task->id}}" role="tabpanel"
                         aria-labelledby="tasks-tab{{$task->id}}">


                        <div class="row">
                            <div class="col">
                                @if ($task->is_star)
                                    <div class="alert alert-success" role="alert">
                                        <strong>Это необязательная задача.</strong> За ее решение вы получите
                                        дополнительные
                                        баллы.
                                    </div>
                                @endif

                                <div class="card">
                                    <div class="card-header">
                                        {{$task->name}}
                                        @if ($user->role=='teacher')
                                            <a class="float-right btn btn-danger btn-sm"
                                               href="{{url('/insider/tasks/'.$task->id.'/delete')}}">Удалить</a>
                                            <a style="margin-right: 5px;" class="float-right btn btn-success btn-sm"
                                               href="{{url('/insider/tasks/'.$task->id.'/edit')}}">Редактировать</a>
                                            <a style="margin-right: 5px;" class="float-right btn btn-primary btn-sm"
                                               href="{{url('/insider/tasks/'.$task->id.'/phantom')}}">Фантомное
                                                решение</a>
                                        @endif

                                    </div>
                                    <div class="card-body markdown">
                                        @parsedown($task->text)

                                        <span class="badge badge-secondary">Максимальный балл: {{$task->max_mark}}</span>
                                    </div>
                                </div>

                            </div>

                        </div>
                        @foreach ($task->solutions as $key => $solution)
                            @if ($solution->user_id == Auth::User()->id)
                                <div class="row" style="margin-top: 15px; margin-bottom: 15px;">

                                    <div class="col">

                                        <div class="card">
                                            <div class="card-header">
                                                Дата сдачи: {{ $solution->submitted->format('d.M.Y H:m')}}
                                                <div class="float-right">
                                                    @if ($solution->mark!=null)
                                                        <span class="badge badge-primary">Оценка: {{$solution->mark}}</span>
                                                        <br>
                                                    @else
                                                        <span class="badge badge-secondary">Решение еще не проверено</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                {{$solution->text}}
                                                <br><br>
                                                @if ($solution->mark!=null)
                                                    <p>
                                                <span class="badge badge-light">Проверено: {{$solution->checked}}
                                                    , {{$solution->teacher->name}}</span>
                                                    </p>
                                                    <p>
                                                        <span class="small">{{$solution->comment}}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="row" style="margin-top: 15px; margin-bottom: 15px;">

                            <div class="col">

                                <div class="card">
                                    <div class="card-header">
                                        Добавить решение
                                    </div>
                                    <div class="card-body">
                                        <form action="{{url('/insider/tasks/'.$task->id.'/solution')}}" method="POST"
                                              class="form-horizontal">
                                            {{ csrf_field() }}

                                            <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
                                                <label for="text{{$task->id}}" class="col-md-4">Текст ответа</label>

                                                <div class="col-md-12">
                                                <textarea id="text{{$task->id}}" class="form-control"
                                                          name="text">{{old('text')}}</textarea>

                                                    <small class="text-muted">Пожалуйста, не используйте это поле для
                                                        отправки
                                                        исходного кода. Выложите код на <a target="_blank"
                                                                                           href="https://paste.geekclass.ru">GeekPaste</a>,
                                                        <a target="_blank"
                                                           href="https://pastebin.com">pastebin</a>, <a target="_blank"
                                                                                                        href="https://gist.github.com">gist</a>
                                                        или <a target="_blank"
                                                               href="https://paste.ofcode.org/">paste.ofcode</a>, а
                                                        затем
                                                        скопируйте ссылку сюда.<br>Для загрузки картинок и небольших
                                                        файлов можно использовать <a
                                                                href="https://storage.geekclass.ru/" target="_blank">storage.geekclass.ru</a>.
                                                    </small>
                                                    @if ($errors->has('text'))
                                                        <br><span
                                                                class="help-block error-block"><strong>{{ $errors->first('text') }}</strong></span>
                                                    @endif

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-success">Отправить</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавление задачи</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{url('/insider/steps/'.$step->id.'/task')}}" method="POST"
                          class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4">Название</label>

                            <div class="col-md-12">
                                <input type="text" name="name" class="form-control" id="name"/>
                                @if ($errors->has('name'))
                                    <span class="help-block error-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('max_mark') ? ' has-error' : '' }}">
                            <label for="max_mark" class="col-md-4">Максимальный балл</label>

                            <div class="col-md-12">
                                <input type="text" name="max_mark" class="form-control" id="max_mark"/>
                                @if ($errors->has('max_mark'))
                                    <span class="help-block error-block">
                                        <strong>{{ $errors->first('max_mark') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
                            <label for="text" class="col-md-4">Текст вопроса</label>

                            <div class="col-md-12">
                                                <textarea id="text" class="form-control"
                                                          name="text">{{old('text')}}</textarea>

                                @if ($errors->has('text'))
                                    <span class="help-block error-block">
                                        <strong>{{ $errors->first('text') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="is_star">Дополнительное</label>
                            <input type="checkbox" id="is_star" name="is_star" value="on"/>
                        </div>
                        <div class="form-group">
                            <label for="only_class">Только для очной формы</label>
                            <input type="checkbox" id="only_class" name="only_class" value="on"/>
                        </div>
                        <div class="form-group">
                            <label for="only_remote">Только для заочной формы</label>
                            <input type="checkbox" id="only_remote" name="only_remote" value="on"/>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Создать</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var simplemde_task = new SimpleMDE({
            spellChecker: false,
            element: document.getElementById("text")
        });

        $('table').addClass('table table-striped');
    </script>




@endsection
