<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ClientBaseController
{
    public function edit()
    {
        $contact = $this->getContact();
        return view('client.profile.edit', compact('contact'));
    }

    public function update(Request $request)
    {
        $contact = $this->getContact();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phonenumber' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
            'current_password' => 'required_with:password|string',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $contact->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        }

        $updateData = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phonenumber' => $request->phonenumber,
            'title' => $request->title,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['last_password_change'] = now();
        }

        $contact->update($updateData);

        return redirect()->route('client.profile.edit')->with('success', 'Profile updated successfully');
    }
}
