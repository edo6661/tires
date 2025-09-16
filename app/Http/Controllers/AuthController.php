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

/**
 * Authentication Controller
 *
 * Handles all web-based authentication operations including:
 * - User login and logout
 * - User registration
 * - Password reset functionality
 * - Form display for authentication pages
 *
 * This controller manages session-based authentication for the web interface
 * and redirects users based on their roles (admin vs customer)
 *
 * @tags Authentication
 */
class AuthController extends Controller
{
    /**
     * Dependency injection for authentication service
     *
     * @param AuthServiceInterface $authService Service layer handling auth business logic
     */
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    /**
     * Display the login form
     *
     * Shows the login page with optional redirect parameter for post-login navigation.
     * Used when users need to authenticate before accessing protected pages.
     *
     * @param Request $request HTTP request containing optional 'redirect' parameter
     * @return View Returns the login form view with redirect URL if provided
     */
    public function showLoginForm(Request $request): View
    {
        return view('auth.login', [
            'redirect' => $request->get('redirect')
        ]);
    }

    /**
     * Process user login
     *
     * Authenticates user credentials and handles post-login redirection based on:
     * - Custom redirect URL (if provided)
     * - User role (admin → admin dashboard, customer → home)
     *
     * Includes session regeneration for security and error handling for invalid credentials.
     *
     * @param LoginRequest $request Validated login form data (email, password, optional redirect)
     * @return RedirectResponse Redirects to appropriate page or back with errors
     */
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

    /**
     * Display the user registration form
     *
     * Shows the registration page with optional redirect parameter for post-registration navigation.
     * Allows new users to create accounts in the system.
     *
     * @param Request $request HTTP request containing optional 'redirect' parameter
     * @return View Returns the registration form view with redirect URL if provided
     */
    public function showRegisterForm(Request $request): View
    {
        return view('auth.register', [
            'redirect' => $request->get('redirect')
        ]);
    }

    /**
     * Process user registration
     *
     * Creates a new user account with validated data and automatically logs them in.
     * Handles post-registration redirection similar to login (custom URL or role-based).
     *
     * @param UserRequest $request Validated registration form data
     * @return RedirectResponse Redirects to appropriate page based on role or custom redirect
     */
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

    /**
     * Display the forgot password form
     *
     * Shows the password reset request page where users can enter their email
     * to receive a password reset link.
     *
     * @return View Returns the forgot password form view
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link via email
     *
     * Processes the forgot password request by sending a reset link to the provided email.
     * Validates email existence and handles success/error responses.
     *
     * @param ForgotPasswordRequest $request Validated request containing email address
     * @return RedirectResponse Returns back with success message or error
     */
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $sent = $this->authService->sendPasswordResetLink($request->email);
        if ($sent) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }
        return back()->withErrors(['email' => 'Email does not exist']);
    }

    /**
     * Display the password reset form
     *
     * Shows the password reset page with the reset token and email pre-filled.
     * Users access this via the link sent to their email.
     *
     * @param Request $request HTTP request containing email parameter
     * @param string $locale Application locale parameter from route
     * @param string $token Password reset token from the reset link
     * @return View Returns the reset password form with token and email
     */
    public function showResetPasswordForm(Request $request, $locale, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Process password reset
     *
     * Validates the reset token and updates the user's password.
     * Handles token validation, password update, and appropriate redirection.
     *
     * @param ResetPasswordRequest $request Validated request with email, token, and new password
     * @return RedirectResponse Redirects to login on success or back with errors
     */
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

    /**
     * Log out the current user
     *
     * Terminates the user session and redirects to the login page.
     * Clears authentication state and session data.
     *
     * @return RedirectResponse Redirects to login page
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}
