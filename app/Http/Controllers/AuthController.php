<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Services\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        
        if ($this->authService->login($credentials)) {
            $request->session()->regenerate();
            $user = $this->authService->getCurrentUser();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email or password is incorrect.',
        ])->withInput($request->except('password'));
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        $user = $this->authService->register($data);
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('customer.dashboard');
    }
    public function showResetPasswordForm(): View
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        
        return redirect()->route('reset-password')->with('status', 'Password reset link sent to your email.');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        
        return redirect()->route('login');
    }
}