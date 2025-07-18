<?php
// ReservationFactory.php
namespace Database\Factories;

use App\Models\User;
use App\Models\Menu;
use App\Models\BlockedPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $hasUser = fake()->boolean(70);
        $baseData = [
            'reservation_number' => 'RSV' . fake()->unique()->numerify('######'),
            'menu_id' => fn() => Menu::inRandomOrder()->first()->id,
            'reservation_datetime' => $this->generateValidReservationDateTime(),
            'number_of_people' => fake()->numberBetween(1, 8),
            'amount' => fake()->randomFloat(2, 1000, 20000),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'notes' => fake()->optional()->paragraph(),
        ];

        if ($hasUser) {
            $baseData['user_id'] = fn() => User::inRandomOrder()->first()->id;
            $baseData['full_name'] = null;
            $baseData['full_name_kana'] = null;
            $baseData['email'] = null;
            $baseData['phone_number'] = null;
        } else {
            $baseData['user_id'] = null;
            $baseData['full_name'] = fake()->name();
            $baseData['full_name_kana'] = fake()->name();
            $baseData['email'] = fake()->email();
            $baseData['phone_number'] = fake()->phoneNumber();
        }

        return $baseData;
    }

    private function generateValidReservationDateTime(): \DateTime
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            // Generate datetime dengan constraint jam 8:00 - 20:00
            $proposedDateTime = $this->generateBusinessHourDateTime();
            $attempts++;

            if ($attempts > $maxAttempts) {
                // Fallback ke periode yang lebih jauh
                return $this->generateBusinessHourDateTime('+2 months', '+3 months');
            }
        } while ($this->isDateTimeBlocked($proposedDateTime));

        return $proposedDateTime;
    }

    private function generateBusinessHourDateTime(string $from = 'now', string $to = '+1 month'): \DateTime
    {
        $baseDateTime = fake()->dateTimeBetween($from, $to);
        
        // Set jam antara 8:00 - 20:00 dan menit selalu 00
        $hour = fake()->numberBetween(8, 20);
        
        return $baseDateTime->setTime($hour, 0, 0);
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

    public function withValidMenuAndDateTime(): static
    {
        return $this->state(function (array $attributes) {
            $maxAttempts = 100;
            $attempts = 0;

            do {
                $menuId = Menu::inRandomOrder()->first()->id;
                $proposedDateTime = $this->generateBusinessHourDateTime();
                $attempts++;

                if ($attempts > $maxAttempts) {
                    $proposedDateTime = $this->generateBusinessHourDateTime('+2 months', '+3 months');
                    break;
                }
            } while ($this->isDateTimeAndMenuBlocked($proposedDateTime, $menuId));

            return [
                'menu_id' => $menuId,
                'reservation_datetime' => $proposedDateTime,
            ];
        });
    }

    public function guest(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'full_name' => fake()->name(),
                'full_name_kana' => fake()->name(),
                'email' => fake()->email(),
                'phone_number' => fake()->phoneNumber(),
            ];
        });
    }

    public function withUser(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => fn() => User::inRandomOrder()->first()->id,
                'full_name' => null,
                'full_name_kana' => null,
                'email' => null,
                'phone_number' => null,
            ];
        });
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
}
