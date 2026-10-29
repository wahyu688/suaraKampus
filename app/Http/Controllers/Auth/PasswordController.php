<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit(Request $request)
    {
        return view('auth.password', ['user' => $request->user()]);
    }

    public function update(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return redirect($user->homeUrl())->with('success', 'Password berhasil diganti.');
    }
}
