<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Services\MenuService;
use App\Repositories\MenuRepository;

echo "=== Testing Menu Translations Fix ===\n";

// Test direct model access
echo "\n1. Direct Model Access:\n";
$menu = Menu::first();
if ($menu) {
    echo "Menu ID: {$menu->id}\n";
    echo "Available translations:\n";
    foreach ($menu->translations as $translation) {
        echo "  - {$translation->locale}: {$translation->name}\n";
    }
}

// Test repository access
echo "\n2. Repository Access:\n";
$repository = new MenuRepository(new Menu);
$menuFromRepo = $repository->findById(1);
if ($menuFromRepo) {
    echo "Menu ID: {$menuFromRepo->id}\n";
    echo "Available translations:\n";
    foreach ($menuFromRepo->translations as $translation) {
        echo "  - {$translation->locale}: {$translation->name}\n";
    }
}

// Test resource output
echo "\n3. MenuResource Output:\n";
if ($menu) {
    $resource = new MenuResource($menu);
    $resourceArray = $resource->toArray(request());
    
    echo "Translations in resource:\n";
    foreach ($resourceArray['translations'] as $locale => $translation) {
        echo "  - {$locale}: {$translation['name']}\n";
    }
    
    // Show the main name/description fields based on locale
    echo "\nMain fields (current locale):\n";
    echo "  - Name: {$resourceArray['name']}\n";
    echo "  - Description: {$resourceArray['description']}\n";
    echo "  - Meta locale: {$resourceArray['meta']['locale']}\n";
    echo "  - Fallback used: " . ($resourceArray['meta']['fallback_used'] ? 'Yes' : 'No') . "\n";
}

// Test with Japanese locale
echo "\n4. Testing with Japanese Locale:\n";
app()->setLocale('ja');

if ($menu) {
    $resource = new MenuResource($menu);
    $resourceArray = $resource->toArray(request());
    
    echo "Main fields (Japanese locale):\n";
    echo "  - Name: {$resourceArray['name']}\n";
    echo "  - Description: {$resourceArray['description']}\n";
    echo "  - Meta locale: {$resourceArray['meta']['locale']}\n";
    echo "  - Fallback used: " . ($resourceArray['meta']['fallback_used'] ? 'Yes' : 'No') . "\n";
    
    echo "\nAll translations still available:\n";
    foreach ($resourceArray['translations'] as $locale => $translation) {
        echo "  - {$locale}: {$translation['name']}\n";
    }
}

echo "\n=== Test Complete ===\n";