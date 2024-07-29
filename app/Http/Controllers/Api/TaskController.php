<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Traits\Api\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    use Response;

    public $data;
    public $dataArray = [];

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        // validation rules
        $rules = ['name' => 'required', 'room_id' => 'required|int|exists:rooms,id', 'status' => 'required|int', 'durability' => 'required|int',];

        // validation messages
        $messages = ['name.required' => 'Please enter a name.', 'room_id.required' => 'Please enter a room id.', 'status.required' => 'Please enter a status.', 'durability.required' => 'Please enter a durability.',];

        // perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // gather error messages
            $errors = $validator->messages()->all();
            $collection = collect($this->dataArray);
            $this->dataArray = $collection->merge($errors);

            // prepare response
            $this->data = ['status_code' => 200, 'code' => 100499, 'response' => '', 'error' => $this->dataArray, 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // store data
        $data = $request->all();
        $data['created_by'] = Auth::id(); //authenticated user

        Task::create($data);

        // fetch the newly created task
        $task = Task::where('name', $request->name)->first();

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => 'Record stored successfully', 'data' => $task];
        $this->setResponse($this->data);
        return $this->getResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function edit(Request $request, string $id)
    {

        // validation rules
        $rules = ['name' => 'required', 'room_id' => 'required|int|exists:rooms,id', 'status' => 'required|int', 'durability' => 'required|int',];

        // validation messages
        $messages = ['name.required' => 'Please enter a name.', 'room_id.required' => 'Please enter a room id.', 'status.required' => 'Please enter a status.', 'durability.required' => 'Please enter a durability.',];

        // perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // gather error messages
            $errors = $validator->messages()->all();
            $collection = collect($this->dataArray);
            $this->dataArray = $collection->merge($errors);

            // prepare response
            $this->data = ['status_code' => 200, 'code' => 100499, 'response' => '', 'error' => $this->dataArray, 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // find the task to update
        $task = Task::find($id);

        if (!$task) {
            // task not found
            $this->data = ['status_code' => 404, 'code' => 100404, 'response' => '', 'error' => ['Task not found.'], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // update the task data
        $data = $request->all();
        $data['updated_by'] = Auth::id(); //authenticated user


        $task->update($data);

        // fetch the updated task
        $updatedTask = Task::find($id);

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => 'Record updated successfully', 'data' => $updatedTask];
        $this->setResponse($this->data);
        return $this->getResponse();


    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // find the task to delete
        $task = Task::find($id);

        if (!$task) {
            // task not found
            $this->data = ['status_code' => 404, 'code' => 100404, 'response' => '', 'error' => ['Task not found.'], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // delete the task
        $data = $task->delete();

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => 'Record deleted successfully', 'data' => []];
        $this->setResponse($this->data);
        return $this->getResponse();
    }

    public function changeStatus(Request $request, string $id)
    {
        // validation rules
        $rules = ['member' => 'required', 'status' => 'required|int',];

        // validation messages
        $messages = ['member.required' => 'Please enter a name.', 'status.required' => 'Please enter a status.',];

        // perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // gather error messages
            $errors = $validator->messages()->all();
            $collection = collect($this->dataArray);
            $this->dataArray = $collection->merge($errors);

            // prepare response
            $this->data = ['status_code' => 200, 'code' => 100499, 'response' => '', 'error' => $this->dataArray, 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // find the task to update
        $task = Task::find($id);

        if (!$task) {
            // task not found
            $this->data = ['status_code' => 404, 'code' => 100404, 'response' => '', 'error' => ['Task not found.'], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // update the task status
        $data = $request->all();
        $data['updated_by'] = Auth::id(); // authenticated user


        $task->update($data);

        // fetch the updated task
        $updatedTask = Task::find($id);

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => 'Status updated successfully', 'data' => $updatedTask];
        $this->setResponse($this->data);
        return $this->getResponse();


    }
}
