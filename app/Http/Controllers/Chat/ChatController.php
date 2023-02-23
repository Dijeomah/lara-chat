<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function rooms(){
        $rooms = ChatRoom::all();
        return $this->dataResponse(Response::HTTP_OK, 'Rooms fetched', $rooms);
    }

    public function messages(Request $request, $roomId){
        $messages = ChatMessage::where('chat_room_id', $roomId)->with('user')->orderBy('created_at', 'DESC')->get();
        return $this->dataResponse(Response::HTTP_OK, 'Messages fetched', $messages);
    }

    public function newMessage(Request $request, $roomId){

//check if there is a previous conversation, if there is non, create one, if there is, continue chat
        $user = $request->user();

        $prevChat = ChatMessage::where('user_id', $user->id)->first();

        if ($prevChat){
            $newMessage = new ChatMessage;
            $newMessage->admin_id = $prevChat->admin_id;
            $newMessage->user_id = Auth::id();
            $newMessage->chat_room_id = $roomId;
            $newMessage->message = $request->message;
            $newMessage->save();
            return $this->dataResponse(Response::HTTP_OK, 'Message sent.', $newMessage);
        }

        $admin = User::inRandomOrder()->where(['role' => 'admin', 'engagement_status' => '0'])->first();
        $newMessage = new ChatMessage;
        $newMessage->admin_id = $admin->id;
        $newMessage->user_id = Auth::id();
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $request->message;
        $newMessage->save();

//        update the User table and set the engagement to 1.
        User::where('id', $admin->id)->update([
            'engagement_status' => '1'
        ]);

        return $this->dataResponse(Response::HTTP_OK, 'Message sent.', $newMessage);
    }






}
