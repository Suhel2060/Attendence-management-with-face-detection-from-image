<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //
    public function index(){
        $user_id=Auth::user()->id;
        $user=User::find($user_id);

        return view('pages.profile',compact('user'));
    }
}
