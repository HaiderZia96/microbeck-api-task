<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\Api\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
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
        $rules = ['name' => 'required|unique:rooms,name'];

        // validation messages
        $messages = ['name.required' => 'Please enter a name.', 'name.unique' => 'A room with this name already exists.'];

        // perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Gather error messages
            $errors = $validator->messages()->all();
            $collection = collect($this->dataArray);
            $this->dataArray = $collection->merge($errors);

            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => $this->dataArray,
                'data' => []
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // store data
        $data = $request->all();
        $data['created_by'] = Auth::id(); //authenticated user

        Room::create($data);

        // fetch the newly created room
        $room = Room::where('name', $request->name)->first();

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '',
            "success" =>["Record store successfully."],
            'data' => [
                $room
            ]
        ];
        $this->setResponse($this->data);
        return $this->getResponse();
    }


    /**
     * Update the specified resource in storage.
     */
    public function edit(Request $request, string $id)
    {
        // find the room to update
        $room = Room::find($id);

        if (!$room) {
            // room not found
            $this->data = ['status_code' => 200, 'code' => 100402, 'response' => '', 'success' => ['Room not found.'], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // Validation rules
        $rules = ['name' => 'required|unique:rooms,name,' . $id];

        //validation messages
        $messages = ['name.required' => 'Please enter a name.', 'name.unique' => 'A room with this name already exists.',];

        // perform validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // gather error messages
            $errors = $validator->messages()->all();
            $collection = collect($this->dataArray);
            $this->dataArray = $collection->merge($errors);

            // prepare response
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', 'success' => $this->dataArray, 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // find the room to update
//        $room = Room::find($id);
//
//        if (!$room) {
//            // room not found
//            $this->data = ['status_code' => 200, 'code' => 100402, 'response' => '', 'success' => ['Room not found.'], 'data' => []];
//            $this->setResponse($this->data);
//            return $this->getResponse();
//        }

        // Update the room data
        $data = $request->all();
        $data['updated_by'] = Auth::id(); // Use the ID of the currently authenticated user


        $room->update($data);

        // fetch the updated room
        $updatedRoom = Room::find($id);

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => ['Record updated successfully'], 'data' => $updatedRoom];
        $this->setResponse($this->data);
        return $this->getResponse();


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // find the room to delete
        $room = Room::find($id);

        if (!$room) {
            // room not found
            $this->data = ['status_code' => 200, 'code' => 100402, 'response' => '', 'success' => ['Room not found.'], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        // delete the room
        $data = $room->delete();

        // prepare response
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', 'success' => ['Record deleted successfully'], 'data' => []];
        $this->setResponse($this->data);
        return $this->getResponse();
    }
}
