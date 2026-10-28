<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()->load('profile')]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'bio' => ['nullable', 'string', 'max:500'],
            'timezone' => ['required', Rule::in(array_keys(config('habitude.timezones')))],
            'daily_goal' => ['required', 'integer', 'min:1', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user->fill(collect($data)->only(['name', 'email'])->all());
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        $user->profile()->updateOrCreate([], collect($data)->only(['bio', 'timezone', 'daily_goal'])->all());

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }
}
