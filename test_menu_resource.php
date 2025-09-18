<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Resources\MenuResource;
use App\Models\Menu;

echo "=== Testing MenuResource with All Translations ===\n";

// Get a menu with all translations
$menu = Menu::with('translations')->first();

if ($menu) {
    echo "Menu ID: {$menu->id}\n";
    echo "Available translations:\n";
    foreach ($menu->translations as $translation) {
        echo "  - {$translation->locale}: {$translation->name}\n";
    }
    
    echo "\n=== MenuResource Output ===\n";
    $resource = new MenuResource($menu);
    $resourceArray = $resource->toArray(request());
    
    echo "Translations in resource:\n";
    foreach ($resourceArray['translations'] as $locale => $translation) {
        echo "  - {$locale}: {$translation['name']}\n";
    }
    
    echo "\nFull resource data:\n";
    echo json_encode($resourceArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "No menus found\n";
}