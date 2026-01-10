<?php
require_once 'vendor/autoload.php';
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$email = 'admin@admin.com';
$password = 'password';
$name = 'Admin User';

if (User::where('email', $email)->exists()) {
    echo "Admin user already exists: $email\n";
} else {
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
    ]);
    echo "Admin user created: $email\n";
}
