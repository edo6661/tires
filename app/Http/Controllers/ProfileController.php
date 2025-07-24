<?php
namespace App\Http\Controllers;
use App\Services\AuthServiceInterface;
use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class ProfileController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected AuthServiceInterface $authService,
    ) {}
    public function show()
    {
        $user = $this->authService->getCurrentUser();
        return view('profile.show', compact('user'));
    }
    public function edit()
    {
        $user = $this->authService->getCurrentUser();
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = $this->authService->getCurrentUser();
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'full_name_kana' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            
            'company_name' => 'nullable|string|max:255',
            'home_address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);
        $this->userService->updateUser($user->id, $validatedData);
        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }
    public function updatePassword(Request $request)
    {
        $user = $this->authService->getCurrentUser();
        $validatedData = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $this->userService->updateUser($user->id, [
            'password' => $validatedData['new_password'],
        ]);
        return redirect()->route('profile.show')->with('success', 'Password berhasil diubah.');
    }
}