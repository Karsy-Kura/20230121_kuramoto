<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Models\Tag;
use App\Http\Requests\TodoRequest;
use App\Models\User;
use Hamcrest\Core\IsNull;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class TodoController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $tags = Tag::all();
    $param = [
      'user' => $user,
      'todos' => $user->todos,
      'tags' => $tags
    ];
    return view('index', $param);
  }

  public function store(TodoRequest $request)
  {
    $user = Auth::user();

    $form = $request->all();
    unset($form['_token']);

    $new_todo = new Todo();
    $new_todo->user_id = $user->id;
    $new_todo->fill($form)->save();

    return redirect('/');
  }

  public function update(TodoRequest $request)
  {
    $todo = Todo::find($request->id);
    $form = $request->all();
    unset($form['_token']);
    $todo->fill($form)->save();
    return redirect('/');
  }

  public function delete(Request $request)
  {
    $user = Auth::user();
    $condition = [
      ['user_id', '=', $user->id],
      ['id', '=', $request->id]
    ];

    Todo::where($condition)->delete();
    return redirect('/');
  }

  public function find()
  {
    $user = Auth::user();
    $tags = Tag::all();
    $param = [
      'user' => $user,
      'todos' => [],
      'tags' => $tags
    ];
    return view('find', $param);
  }

  public function search(Request $request)
  {
    $user = Auth::user();
    $query = $request->query();
    unset($query['_token']);

    $content = $query['content'];
    $todos = Todo::where([['user_id', '=', $user->id], ['content', 'LIKE', "%$content%"]])->get();

    if (array_key_exists("tag_id", $query)) {
      $todos = $todos->where('tag_id', '=', $query['tag_id']);
    }

    $tags = Tag::all();
    $param = [
      'user' => $user,
      'todos' => $todos,
      'tags' => $tags
    ];
    return view('find', $param);
  }
}
