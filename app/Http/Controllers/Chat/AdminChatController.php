<?php

    namespace App\Http\Controllers\Chat;

    use App\Http\Controllers\Controller;
    use App\Models\ChatMessage;
    use App\Models\ChatRoom;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Auth;

    class AdminChatController extends Controller
    {
//        public function __construct()
//        {
//            $this->middleware(['auth:api']);
//        }

        public function rooms()
        {
            $rooms = ChatRoom::all();
            return $this->dataResponse(Response::HTTP_OK, 'Rooms fetched', $rooms);
        }

        public function allMyMessages(Request $request, $roomId){
            $admin = $request->user('admin');
            $adminMessages = ChatMessage::where(['admin_id' => $admin->id, 'chat_room_id' => $roomId])->orderBy('created_at', 'DESC')->get();
            return $this->dataResponse(Response::HTTP_OK, 'Message fetched.', $adminMessages);
        }


        //get messages of the logged admin
        public function myMessages(Request $request, $roomId, $userId)
        {
            $admin = $request->user('admin');
            $adminMessages = ChatMessage::where(['admin_id' => $admin->id, 'chat_room_id' => $roomId, 'user_id' => $userId])->with('user')->orderBy('created_at', 'DESC')->get();
            return $this->dataResponse(Response::HTTP_OK, 'Message fetched.', $adminMessages);
        }

//    public function sendMessage(Request $request, $roomId, $userId){
        public function sendMessage(Request $request, $roomId, $userId)
        {

            $admin = $request->user('admin');
//
            $prevChat = ChatMessage::where(['admin_id' => $admin->id, 'chat_room_id' => $roomId, 'user_id' => $userId])->first();

            if ($prevChat) {
                $newMessage = new ChatMessage;
                $newMessage->admin_id = $admin->id;
//        $newMessage->user_id = $prevChat->user_id;
                $newMessage->user_id = $userId;
                $newMessage->chat_room_id = $roomId;
                $newMessage->message = $request->message;
                $newMessage->save();
                return $this->dataResponse(Response::HTTP_OK, 'Message sent.', $newMessage);

            }
            return $this->dataResponse(Response::HTTP_CONFLICT, 'Error.', []);
        }

        public function updateEngagementStatus(Request $request){
            $admin = $request->user('admin');
            $statusUpdate = User::where(['id'=>$admin->id,'role'=>'admin'])->update([
               'engagement_status'=>'0'
            ]);
            return $this->dataResponse(Response::HTTP_OK, 'Chat ended.', $statusUpdate);
        }
    }
