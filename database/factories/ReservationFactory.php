<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Menu;
use App\Enums\ReservationStatus;
use App\Models\BlockedPeriod;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_number' => 'RSV' . fake()->unique()->numerify('######'),
            'user_id' => fn() => User::inRandomOrder()->first()->id,
            'menu_id' => fn() => Menu::inRandomOrder()->first()->id,
            'reservation_datetime' => $this->generateValidReservationDateTime(),
            'number_of_people' => fake()->numberBetween(1, 8),
            'amount' => fake()->randomFloat(2, 1000, 20000),
            'status' => fake()->randomElement(ReservationStatus::values()),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    private function generateValidReservationDateTime(): \DateTime
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $proposedDateTime = fake()->dateTimeBetween('now', '+1 month');
            $attempts++;
            
            if ($attempts > $maxAttempts) {
                
                return fake()->dateTimeBetween('+2 months', '+3 months');
            }
            
        } while ($this->isDateTimeBlocked($proposedDateTime));
        
        return $proposedDateTime;
    }

    private function isDateTimeBlocked(\DateTime $dateTime): bool
    {
        $blockedPeriods = BlockedPeriod::where(function ($query) use ($dateTime) {
            $query->where('start_datetime', '<=', $dateTime)
                  ->where('end_datetime', '>=', $dateTime);
        })->get();

        foreach ($blockedPeriods as $blockedPeriod) {
            
            if ($blockedPeriod->all_menus) {
                return true;
            }
            
            
            if ($blockedPeriod->menu_id) {
                return true; 
            }
        }

        return false;
    }

    private function isDateTimeAndMenuBlocked(\DateTime $dateTime, $menuId): bool
    {
        return BlockedPeriod::where(function ($query) use ($dateTime) {
            $query->where('start_datetime', '<=', $dateTime)
                  ->where('end_datetime', '>=', $dateTime);
        })->where(function ($query) use ($menuId) {
            $query->where('all_menus', true)
                  ->orWhere('menu_id', $menuId);
        })->exists();
    }

    public function withValidMenuAndDateTime(): static
    {
        return $this->state(function (array $attributes) {
            $maxAttempts = 100;
            $attempts = 0;
            
            do {
                $menuId = Menu::inRandomOrder()->first()->id;
                $proposedDateTime = fake()->dateTimeBetween('now', '+1 month');
                $attempts++;
                
                if ($attempts > $maxAttempts) {
                    
                    $proposedDateTime = fake()->dateTimeBetween('+2 months', '+3 months');
                    break;
                }
                
            } while ($this->isDateTimeAndMenuBlocked($proposedDateTime, $menuId));
            
            return [
                'menu_id' => $menuId,
                'reservation_datetime' => $proposedDateTime,
            ];
        });
    }
}
