<?php

namespace App\Modules\ChatApp\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatPublicMessage;

class ChatAppController
{
    public function webPublicMessage()
    {
        return view("ChatApp::web.public-message");
    }

    public function webPublicMesssageSend(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        //event(new ChatPublicMessage($request->username, $request->message));
         broadcast(new ChatPublicMessage($request->username, $request->message));

        return response()->json(['status' => 'Message sent']);
    }
}
