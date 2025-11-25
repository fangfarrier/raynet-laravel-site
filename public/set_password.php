<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
// Boot Laravel so the container + facades work
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "<pre>";

try {
    // CHANGE THIS to the callsign of the user you just created
    $callsign = 'M0ADM';

    // CHANGE THIS to whatever password you want to give them
    $newPassword = 'TempPass123!';

    $user = User::where('callsign', $callsign)->first();

    if (! $user) {
        echo "User not found with callsign: {$callsign}\n";
        exit;
    }

    $user->password = Hash::make($newPassword);
    $user->save();

    echo "Password updated successfully.\n";
    echo "User:  {$user->name} ({$user->callsign})\n";
    echo "New password: {$newPassword}\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>";