<?php

namespace App\Http\Controllers;

use App\Events\InquirySubmitted;
use App\Services\BusinessSettingServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\MenuServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct(
        protected MenuServiceInterface $menuService,
        protected BusinessSettingServiceInterface $businessSettingService,
        protected ContactServiceInterface $contactService
    ) {}
    public function index()
    {
        $menus = $this->menuService->getAllMenus();
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('home', compact('businessSettings', 'menus'));
    }
    public function about()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('about', compact('businessSettings'));
    }
    public function terms()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('terms', compact('businessSettings'));
    }
    public function privacy()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('privacy', compact('businessSettings'));
    }
    public function inquiry()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('inquiry', compact('businessSettings'));
    }
    public function submitInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');
        }
        $contactData = [
            'user_id' => Auth::check() ? Auth::id() : null,
            'full_name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ];
        $contact = $this->contactService->createContact($contactData);
        event(new InquirySubmitted($contact));
        return back()->with('success', 'Thank you for your inquiry! We will get back to you soon.');
    }

    public function contact()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('contact', compact('businessSettings'));
    }

    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('contact.error_message'));
        }

        $contactData = [
            'user_id' => Auth::id(), // Akan null jika user tidak login
            'full_name' => $request->name,
            'email' => $request->email,
            'phone_number' => null, // Tidak ada field phone di form contact
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        $contact = $this->contactService->createContact($contactData);

        event(new InquirySubmitted($contact));

        return back()->with('success', __('contact.success_message'));
    }
}
