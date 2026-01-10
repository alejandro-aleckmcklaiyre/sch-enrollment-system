<?php
require_once 'vendor/autoload.php';
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$admins = User::where('role', 'admin')->get(['id','name','email','role']);
echo "Admin users:\n";
foreach ($admins as $admin) {
    echo "ID: {$admin->id}, Name: {$admin->name}, Email: {$admin->email}, Role: {$admin->role}\n";
}
if ($admins->isEmpty()) {
    echo "No admin users found!\n";
}