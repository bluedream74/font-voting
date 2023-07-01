<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index() {
        $user = Auth::user();
        if($user->profile_complete_state) {
            return redirect()->route('myItem.index');
        } else {
            return view('profile.index');
        }
    }

    public function confirm(Request $request) {
        return view('profile.confirm');
    }

    public function show(Request $request) {
        return view('profile.show');
    }

    public function update(Request $request, $id) {
        $data = $request->except(['_token', '_method', 'avatar_img', 'avatar_remove', 'avatar', 'zip1', 'zip2']);
        if($request->input('avatar_img')) {
            $imageData = base64_decode($request->input('avatar_img'));
            $avatarName = $request->input('avatar');

            $filename = uniqid() . '.' . strtolower(pathinfo($avatarName, PATHINFO_EXTENSION));

            Storage::disk('public')->put($filename, $imageData);

            $avatarUrl = Storage::disk('public')->url($filename);

            // $data->merge(['avatar_url' => $avatarUrl]);
            $data = array_merge($data, ['avatar_url' => $avatarUrl]);
        }

        $data = array_merge($data, ['postal_code' => $request->input('zip1') . $request->input('zip2')]);

        \App\Models\User::where('id', Auth::user()->id)->update($data);

        return redirect()->route('profile.confirm');
    }
}
