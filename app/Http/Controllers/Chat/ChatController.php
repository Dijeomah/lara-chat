<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
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
        $newMessage = new ChatMessage;
        $newMessage->user_id = Auth::id();
        $newMessage->chat_room_id = $roomId;
        $newMessage->message = $request->message;
        $newMessage->save();

        return $this->dataResponse(Response::HTTP_OK, 'Message sent.', $newMessage);
    }
}
