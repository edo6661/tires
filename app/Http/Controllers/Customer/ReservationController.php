<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Services\ReservationServiceInterface;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationServiceInterface $reservationService,
    ) {}
    
    
}