<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Menu;
class BlockedPeriodFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 week');
        
        
        $startDate = $this->setTimeToHourStart($startDate);
        
        $durationMinutes = fake()->randomElement([
            30,   
            60,   
            120,  
            180,  
            240,  
            300,  
            360,  
        ]);
        
        $endDate = (clone $startDate)->modify("+{$durationMinutes} minutes");
        $allMenus = fake()->boolean(20); 
        
        return [
            
            'menu_id' => $allMenus ? null : (Menu::inRandomOrder()->value('id') ?? Menu::factory()),
            'start_datetime' => $startDate,
            'end_datetime' => $endDate,
            'reason' => fake()->randomElement([
                'System maintenance',
                'Menu update',
                'Out of stock temporarily',
                'Special promotion',
                'Kitchen cleaning',
                'Staff training',
                'Special event',
            ]),
            'all_menus' => $allMenus,
        ];
    }

    private function setTimeToHourStart(\DateTime $dateTime): \DateTime
    {
        return $dateTime->setTime(
            $dateTime->format('H'), 
            0, 
            0  
        );
    }
    
    public function shortBreak(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('now', '+3 days');
            $startDate = $this->setTimeToHourStart($startDate);
            $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(30, 120) . ' minutes');
            
            return [
                'start_datetime' => $startDate,
                'end_datetime' => $endDate,
                'reason' => 'Short break',
            ];
        });
    }
    
    public function maintenanceHours(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('now', '+1 week');
            $startDate = $this->setTimeToHourStart($startDate);
            $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(2, 4) . ' hours');
            
            return [
                'start_datetime' => $startDate,
                'end_datetime' => $endDate,
                'reason' => 'Maintenance',
            ];
        });
    }

    public function forAllMenus(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'menu_id' => null,
                'all_menus' => true,
            ];
        });
    }

    public function forSpecificMenu(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'menu_id' => Menu::inRandomOrder()->first()->id,
                'all_menus' => false,
            ];
        });
    }
}