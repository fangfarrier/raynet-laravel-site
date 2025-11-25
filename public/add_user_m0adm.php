<?php

// Show any errors directly in the browser (for debugging this script only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1) Bootstrap Laravel (autoload + app + kernel) so models/facades work
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 2) Pull in the User model and Hash facade
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "<pre>";

try {
    // If you re-run this, we don't want duplicates, so check first.
    $callsign = 'M0ADM';

    // OPTIONAL: if you want an email-based login later, set an email now:
    $email = null; // or 'maciek@example.com';

    $existing = User::where('callsign', $callsign)->first();

    if ($existing) {
        echo "User with callsign {$callsign} already exists:\n";
        echo "ID: {$existing->id}\n";
        echo "Name: {$existing->name}\n";
        echo "Email: {$existing->email}\n";
        echo "is_admin: {$existing->is_admin}\n";
    } else {
        // 3) Create the new user record in the *live* users table.
        $user = User::create([
            'name'     => 'Maciek Goszltyla',    // change if needed
            'email'    => 'Maciek.Gosztyla@raynet-uk.net',               // null is allowed if your app doesn't require email
            'password' => Hash::make('TempPass123!'), // temporary password
            'callsign' => 'M0ADM',
            'is_admin' => 1,                    // set to 1 if you want him admin
        ]);

        echo "New user created:\n";
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Callsign: {$user->callsign}\n";
        echo "Password: TempPass123!\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>";