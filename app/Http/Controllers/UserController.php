<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Storage;

class UserController extends ResponseController
{
    /**
     * Get the authenticated user's profile with wallet.
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('wallet');
        return $this->success('Profile retrieved successfully', $user);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $inputs = $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'sex' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path('avatars/' . $user->avatar))) {
                unlink(public_path('avatars/' . $user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '_avatar.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
            $inputs['avatar'] = $avatarName;
        }

        $user->update(array_filter($inputs));

        return $this->success('Profile updated successfully', $user->fresh('wallet'));
    }

    /**
     * Search for a user by phone or account number (for transfers).
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $query = $request->input('query');

        // Search by Phone
        $user = User::where('phone', $query)->first();

        // If not found by phone, search by Wallet Account Number
        if (!$user) {
            $wallet = \App\Models\Wallet::where('account_number', $query)->first();
            if ($wallet) {
                $user = $wallet->user;
            }
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        // Return only safe public info
        return $this->success('User found', [
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'avatar' => $user->avatar,
            'phone' => $user->phone, // Maybe useful for confirmation
        ]);
    }
}
