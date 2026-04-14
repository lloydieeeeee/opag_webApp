<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Handle both "personal" and "accounts" section updates.
     * The form sends a hidden input: name="section" value="personal|accounts"
     */
    public function update(Request $request)
    {
        $employee = Auth::user()->employee;
        $section  = $request->input('section', 'personal');

        if ($section === 'personal') {

            $validated = $request->validate([
                'first_name'     => 'required|string|max:80',
                'last_name'      => 'required|string|max:80',
                'middle_name'    => 'nullable|string|max:80',
                'extension_name' => 'nullable|string|max:20',
                'contact_number' => 'nullable|string|max:30',
                'address'        => 'nullable|string|max:255',
                'birthday'       => 'nullable|date|before:today',
            ]);

            $employee->update($validated);

            return redirect()->route('profile.index')
                             ->with('success', 'Personal details updated.');
        }

        if ($section === 'accounts') {

            $validated = $request->validate([
                'pagibig_id'   => 'nullable|numeric|digits_between:1,20',
                'gsis_id'      => 'nullable|numeric|digits_between:1,20',
                'philhealth_id'=> 'nullable|numeric|digits_between:1,20',
            ]);

            $employee->update($validated);

            return redirect()->route('profile.index')
                             ->with('success', 'Account IDs updated.');
        }

        return redirect()->route('profile.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                         ->withFragment('credentials');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile.index')
                         ->with('success', 'Password updated successfully.');
    }
}