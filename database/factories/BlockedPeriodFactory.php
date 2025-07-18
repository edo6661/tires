<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Menu;

class BlockedPeriodFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->generateBusinessHourDateTime();
        
        $durationMinutes = fake()->randomElement([
            60,   // 1 jam
            120,  // 2 jam
            180,  // 3 jam
            240,  // 4 jam
            300,  // 5 jam
            360,  // 6 jam
        ]);
        
        $endDate = (clone $startDate)->modify("+{$durationMinutes} minutes");
        
        // Pastikan end_datetime tidak melewati jam 20:00
        if ($endDate->format('H') > 20) {
            $endDate->setTime(20, 0, 0);
        }
        
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

    private function generateBusinessHourDateTime(string $from = 'now', string $to = '+1 week'): \DateTime
    {
        $baseDateTime = fake()->dateTimeBetween($from, $to);
        
        // Set jam antara 8:00 - 20:00 dan menit selalu 00
        $hour = fake()->numberBetween(8, 20);
        
        return $baseDateTime->setTime($hour, 0, 0);
    }
    
    public function shortBreak(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateBusinessHourDateTime('now', '+3 days');
            
            // Untuk short break, durasi 1-2 jam
            $durationHours = fake()->numberBetween(1, 2);
            $endDate = (clone $startDate)->modify("+{$durationHours} hours");
            
            // Pastikan tidak melewati jam 20:00
            if ($endDate->format('H') > 20) {
                $endDate->setTime(20, 0, 0);
            }
            
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
            $startDate = $this->generateBusinessHourDateTime('now', '+1 week');
            
            // Untuk maintenance, durasi 2-4 jam
            $durationHours = fake()->numberBetween(2, 4);
            $endDate = (clone $startDate)->modify("+{$durationHours} hours");
            
            // Pastikan tidak melewati jam 20:00
            if ($endDate->format('H') > 20) {
                $endDate->setTime(20, 0, 0);
            }
            
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
