<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnnouncementServiceInterface;
use App\Services\ReservationServiceInterface;
use App\Services\ContactServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct(
        protected AnnouncementServiceInterface $announcementService,
        protected ReservationServiceInterface $reservationService,
        protected ContactServiceInterface $contactService
    ) {
    }

    public function index()
    {
        $announcements = $this->announcementService->getActiveAnnouncements()->take(5);
        
        $todayReservations = $this->reservationService->getTodayReservations();
        
        $pendingContacts = $this->contactService->getPendingContacts()->take(5);
        $pendingContactsCount = $this->contactService->getPendingContacts()->count();
        
        $currentMonth = Carbon::now()->format('Y-m');
        $monthlyReservations = $this->reservationService->getReservationsByDateRange(
            Carbon::now()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->endOfMonth()->format('Y-m-d')
        );
        
        $newCustomersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('role', 'customer')
            ->count();
        
        $onlineReservationsThisMonth = $monthlyReservations->count();
        
        $totalCustomers = User::where('role', 'customer')->count();
        
        $customerLimit = 100;
        $customersUntilLimit = $customerLimit - $totalCustomers;
        
        $lastLogin = auth()->user()->last_login_at ?? Carbon::now();
        
        return view('admin.dashboard', compact(
            'announcements',
            'todayReservations',
            'pendingContactsCount',
            'pendingContacts',
            'monthlyReservations',
            'newCustomersThisMonth',
            'onlineReservationsThisMonth',
            'totalCustomers',
            'customersUntilLimit',
            'lastLogin'
        ));
    }
}