<?php

namespace App\Http\Controllers;

use App\Services\BusinessSettingServiceInterface;
use App\Services\MenuServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function __construct(
        protected MenuServiceInterface $menuService,
        protected BusinessSettingServiceInterface $businessSettingService
    ) {}


    public function index()
    {
        $menus = $this->menuService->getAllMenus();
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        
        return view('home', compact('businessSettings', 'menus'));    
    }
}
