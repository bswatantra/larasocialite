<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
	/**
	 * Display the login view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return view('auth.login');
	}

	/**
	 * Handle an incoming authentication request.
	 *
	 * @param  \App\Http\Requests\Auth\LoginRequest  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(LoginRequest $request)
	{
		$request->authenticate();

		$request->session()->regenerate();

		return redirect(RouteServiceProvider::HOME);
	}

	/**
	 * Destroy an authenticated session.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy(Request $request)
	{
		Auth::guard('web')->logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return redirect('/');
	}

	public function redirectToGoogle()
	{
		return Socialite::driver('google')->redirect();
	}


	public function handleGoogleCallback()
	{
		$user = Socialite::driver('google')->user();
		$this->_registerOrLoginUser($user);
		return redirect()->route('dashboard');
	}


	public function redirectToFacebook()
	{
		return Socialite::driver('facebook')->redirect();
	}


	public function handleFacebookCallback()
	{
		$user = Socialite::driver('facebook')->user();
		$this->_registerOrLoginUser($user);
		return redirect()->route('dashboard');
	}

	public function redirectToGithub()
	{
		return Socialite::driver('github')->redirect();
	}


	public function handleGithubCallback()
	{
		$user = Socialite::driver('github')->user();
		$this->_registerOrLoginUser($user);
		return redirect()->route('dashboard');
	}


	public function _registerOrLoginUser($data)
	{
		// dd($data);
		$user = User::where('email', '=', $data->email)->first();
		if (!$user) {
			$user = new User();
			$user->name = $data->name;
			$user->email = $data->email;
			$user->provider_id = $data->id;
			$user->avatar = $data->avatar;
			$user->save();
		}
		Auth::login($user);
	}
}
