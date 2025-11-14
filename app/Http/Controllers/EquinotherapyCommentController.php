<?php

namespace App\Http\Controllers;

use App\Models\EquinotherapyComment;
use Illuminate\Http\Request;

class EquinotherapyCommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'equinotherapy_schedule_id' => 'required|exists:equinotherapy_schedules,id',
            'comment' => 'required|string'
        ]);

        $comment = EquinotherapyComment::create($data);

        return response()->json(['data' => $comment], 201);
    }

}
