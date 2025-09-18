<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Repositories\BlockedPeriodRepository;
use App\Models\BlockedPeriod;

echo "=== Debugging Calendar Data ===\n";

// Check total records
$total = BlockedPeriod::count();
echo "Total blocked periods in database: {$total}\n";

// Check current month range
$startDate = now()->startOfMonth()->format('Y-m-d');
$endDate = now()->endOfMonth()->format('Y-m-d');
echo "Current month range: {$startDate} to {$endDate}\n";

// Test repository method
$repo = new BlockedPeriodRepository(new BlockedPeriod);
$periods = $repo->getByDateRange($startDate, $endDate);
echo "Found periods in current month: " . $periods->count() . "\n";

// Get some sample data
$samplePeriods = BlockedPeriod::with('menu')->limit(5)->get();
echo "\n=== Sample Data ===\n";
foreach ($samplePeriods as $period) {
    echo "ID: {$period->id}\n";
    echo "Start: {$period->start_datetime}\n";
    echo "End: {$period->end_datetime}\n";
    echo "Menu: " . ($period->menu ? $period->menu->name : 'All Menus') . "\n";
    echo "All Menus: " . ($period->all_menus ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

// Test with broader date range
echo "\n=== Testing broader date range ===\n";
$broadPeriods = $repo->getByDateRange('2024-01-01', '2025-12-31');
echo "Found periods in 2024-2025: " . $broadPeriods->count() . "\n";

// Check the actual query being generated
echo "\n=== Testing actual query ===\n";
$query = BlockedPeriod::with(['menu'])
    ->where(function ($query) use ($startDate, $endDate) {
        $query->whereBetween('start_datetime', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay()
        ])
        ->orWhereBetween('end_datetime', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay()
        ])
        ->orWhere(function ($subQuery) use ($startDate, $endDate) {
            $subQuery->where('start_datetime', '<=', \Carbon\Carbon::parse($startDate)->startOfDay())
                    ->where('end_datetime', '>=', \Carbon\Carbon::parse($endDate)->endOfDay());
        });
    })
    ->orderBy('start_datetime');

echo "SQL Query: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";

$result = $query->get();
echo "Query result count: " . $result->count() . "\n";
