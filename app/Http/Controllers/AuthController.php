<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}
    public function showLoginForm(Request $request): View
    {
        return view('auth.login', [
            'redirect' => $request->get('redirect')
        ]);
    }
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        if ($this->authService->login($credentials)) {
            $request->session()->regenerate();
            $user = $this->authService->getCurrentUser();
            $redirectUrl = $request->get('redirect');
            if ($redirectUrl) {
                return redirect()->to($redirectUrl);
            }
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return back()->withErrors([
            'email' => 'Email or password is incorrect.',
        ])->withInput($request->except('password'));
    }
    public function showRegisterForm(Request $request): View
    {
        return view('auth.register', [
            'redirect' => $request->get('redirect')
        ]);
    }
    public function register(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $this->authService->register($data);
        $redirectUrl = $request->get('redirect');
        if ($redirectUrl) {
            return redirect()->to($redirectUrl);
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $sent = $this->authService->sendPasswordResetLink($request->email);
        if ($sent) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }
        return back()->withErrors(['email' => 'Email does not exist']);
    }
    public function showResetPasswordForm(Request $request, $locale, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $reset = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );
        if ($reset) {
            return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please log in with your new password.');
        }
        return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kedaluwarsa.']);
    }
    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}