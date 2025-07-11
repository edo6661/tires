<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How long does a tire installation take?',
                'answer' => 'A typical tire installation takes about 45-60 minutes, depending on the type of service and number of tires being installed.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Do I need to make an appointment?',
                'answer' => 'Yes, we recommend making an appointment through our online reservation system to ensure we can serve you at your preferred time.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept cash, credit cards (Visa, MasterCard, JCB), and bank transfers.',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Can I bring my own tires?',
                'answer' => 'Yes, you can bring your own tires. We offer installation services for customer-provided tires.',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'How much does tire storage cost?',
                'answer' => 'Tire storage fees vary depending on the size and type of tires. Please contact us for a detailed quote.',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'What information do I need to provide when making a reservation?',
                'answer' => 'When making a reservation, please provide your contact information, preferred date and time, type of service needed, and any special requirements.',
                'display_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::factory()->create($faq);
        }

    }
}
