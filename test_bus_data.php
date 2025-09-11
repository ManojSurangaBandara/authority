<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check existing data
echo "=== BUS TYPES ===\n";
$busTypes = App\Models\BusType::all();
foreach ($busTypes as $type) {
    echo "ID: {$type->id}, Name: {$type->name}\n";
}

echo "\n=== BUSES ===\n";
$buses = App\Models\Bus::all();
foreach ($buses as $bus) {
    echo "ID: {$bus->id}, No: {$bus->no}, Name: {$bus->name}, Type ID: {$bus->type_id}\n";
}

echo "\n=== BUS ROUTES ===\n";
$routes = App\Models\BusRoute::with('bus')->get();
foreach ($routes as $route) {
    echo "ID: {$route->id}, Name: {$route->name}, Bus ID: {$route->bus_id}";
    if ($route->bus) {
        echo ", Bus No: {$route->bus->no}";
    } else {
        echo ", Bus: NULL";
    }
    echo "\n";
}

echo "\n=== BUS DRIVER ASSIGNMENTS ===\n";
$assignments = App\Models\BusDriverAssignment::with('busRoute.bus')->get();
foreach ($assignments as $assignment) {
    $routeName = $assignment->busRoute ? $assignment->busRoute->name : 'N/A';
    echo "ID: {$assignment->id}, Route: {$routeName}, Driver: {$assignment->driver_name}\n";
}
