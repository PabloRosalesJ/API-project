@extends('layout')

@if (session('status'))
    <div>
        <strong>{{ session('status') }}</strong>
    </div>
@endif

@section('content')
    <form action="{{ route('tasks.store') }}" method="post">
        @csrf
        <label>Title</label>
        <input type="text" name="title"><br>
        <label>Body</label>
        <input type="text" name="body"><br>
        <input type="submit" value="Save">
    </form>

    <ul>
        @foreach ($errors->all() as $error)
            <li style="background: rgb(255, 64, 64); color: #fff">{{ $error }}</li>
        @endforeach
    </ul>

    <br>

    @if ($tasks->count())
    All task´s:
    @else
    Nothing yet ...
    @endif

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Body</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
            <tr>
                <td>{{$task->title}}</td>
                <td>{{$task->body}}</td>
                <td>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                        @csrf
                        @method('delete')
                        <input style="background: rgb(255, 64, 64); color: #fff" type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@endsection
