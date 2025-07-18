<?php

namespace App\Modules\Authentication\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Modules\User\Models\AppUser;
use App\Models\User;
use Exception;
use App\Functions\FunctionFile;

class AuthenticationAdminController
{

    public function adminSignUp()
    {
        return view("Authentication::pages.admin.sign-up");
    } // End adminSignUp()

    public function adminSignUpStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:320|unique:users',
            'password' => 'required|string|min:8',
            'gender_code' => 'required|in:M,F,O,N',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'name.required' => 'Name field is required.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email field must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'gender_code.required' => 'Gender is required.',
            'gender_code.in' => 'Invalid gender selected.',
            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a JPG or PNG file.',
            'avatar.max' => 'Avatar size must not exceed 3MB.',
        ]);


        DB::beginTransaction();

        try {
            AppUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender_code' => $request->gender_code,
                'user_unique_id' => uniqid('user_'),
                'password' => Hash::make($request->password),
                'avatar' => $request->file('avatar')
                    ? FunctionFile::uploadImageFile($request->file('avatar'), 'uploads/avatars')
                    : null
            ]);

            DB::commit();

            session()->flash('success', 'Account created successfully. Please log in. [AUTH-1001]');
            return redirect('/admin/sign-up');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('SignUp Error [AUTH-1002]:', [
                'request' => $request->except(['password', 'avatar']),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors(['error' => 'There was an error creating your account. [AUTH-1002]']);
        }
    } // End signUpStore()

    public function adminSignIn()
    {
        return view("Authentication::pages.admin.sign-in");
    } // End adminSignIn()

    public function adminSignInSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        try {
            $user = User::where('email', $request->email)->where('is_active', 1)->first();

            if (!$user) {
                return redirect()->back()->withErrors(['error' => 'Account not found or inactive.']);
            }
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->withErrors(['error' => 'Invalid credentials.']);
            }

            Auth::login($user);

            return redirect()->intended('/admin');
        } catch (Exception $e) {

            Log::error('SignUp Error [AUTH-1002]:', [
                'request' => $request->except(['password']),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'There was an error during login. Please try again later.']);
        }
    }

    public function signOut()
    {
        Auth::logout(); // Logs out the user
        request()->session()->invalidate(); // Invalidates the session
        request()->session()->regenerateToken(); // Regenerates the CSRF token for security
        return redirect('/admin/sign-in');
    }

    public function unAuthenticated(Request $request)
    {

        Log::error('SignUp Error [AUTH-1002]:', [
            'request' => $request,
        ]);

        return view("Authentication::pages.admin.unauthenticated");
    }
}
