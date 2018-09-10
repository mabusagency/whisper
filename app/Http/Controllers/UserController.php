<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Helpers\MailChimpHelper;
use App\Student;
use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Config;

class UserController extends Controller
{

    public function update(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|max:255|unique:users,email,'.Auth::user()->id,
        ]);

        Auth::user()->email = $request->email;
        Auth::user()->save();

        return redirect("/settings/profile?message=Profile+Updated");
    }

    public function update_security(Request $request)
    {

        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if(bcrypt($request->password) != Auth::user()->password) {
            return redirect("/settings/profile?error=Current password is not correct");
        }

        Auth::user()->password = $request->email;
        Auth::user()->save();

        return redirect("/settings/security?message=Password+Updated");
    }


}
