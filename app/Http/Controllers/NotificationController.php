<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        return NotificationResource::collection(auth()->user()->notifications);
    }

    public function markAllAsRead(){
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json([], 200);
    }
}
