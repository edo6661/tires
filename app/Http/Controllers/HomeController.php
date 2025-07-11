<?php

namespace App\Http\Controllers;

use App\Services\MenuServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function __construct(
        protected MenuServiceInterface $menuService
    ) {}


    public function index()
    {
        $menus = $this->menuService->getAllMenus();
        return view('home', compact('menus'));
    }
}
