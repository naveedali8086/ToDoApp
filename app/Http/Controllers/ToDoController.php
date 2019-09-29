<?php

namespace App\Http\Controllers;

use App\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ToDoController extends Controller
{

    public function showToDoList()
    {

        /* Laravel automatically converts the array|collection into proper json response */
        return ToDo::query()
            ->select(['id', 'desc', 'is_completed'])
            ->orderBy('id')
            ->get();

    }


    public function create(Request $request)
    {

        $this->validate($request, ['desc' => 'required|max:255']);

        $model = ToDo::query()->create(['desc' => $request->get('desc'), 'user_id' => auth()->id()]);

        if ($model) {

            return response()->json(['has_err' => false]);

        } else {

            return response()->json(['has_err' => true, 'err' => 'Unable to save, please try again']);

        }

    }


    public function edit(Request $request, $id)
    {

        $data = $this->toDoExistAndBelongsToUser($id);

        if ($data['has_err']) {

            return response()->json(Arr::except($data, ['model']));

        } else {

            $model = $data['model'];

            /* To-do is being marked as completed or incompleted */
            if (Route::currentRouteName() === 'toggle_status') {

                $model->is_completed = !$model->is_completed;

            } else {

                $this->validate($request, ['desc' => 'required|max:255']);

                /* To-do is being updated */
                $model->desc = $request->get('desc');

            }

            if ($model->save()) {

                return response()->json(['has_err' => false]);

            } else {

                return response()->json(['has_err' => true, 'err' => 'Unable to save, please try again']);

            }

        }

    }


    public function delete($id)
    {

        $data = $this->toDoExistAndBelongsToUser($id);

        if ($data['has_err']) {

            return response()->json(Arr::except($data, ['model']));

        } else {

            if ($data['model']->delete()) {

                return response()->json(['has_err' => false]);

            } else {

                return response()->json(['has_err' => true, 'err' => 'Unable to delete, please try again']);

            }

        }

    }


    private function toDoExistAndBelongsToUser($id)
    {

        $has_err = false;
        $err = '';

        $model = ToDo::find($id);

        if ($model) {

            if ($model->user_id !== auth()->id()) {

                /* to-do does not belong to user */
                $has_err = true;
                $err = 'This to-do does not belong to you';

            }

        } else {

            /* to-do does not exist anymore */
            $has_err = true;
            $err = 'This to-do does not exist anymore';

        }

        return ['has_err' => $has_err, 'err' => $err, 'model' => $model];

    }

}
