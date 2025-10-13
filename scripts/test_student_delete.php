<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// find first active student
$student = DB::table('tblstudent')->where(function($q){ $q->where('is_deleted',0)->orWhereNull('is_deleted'); })->first();
if(!$student){ echo "No active student found to delete\n"; exit;
}
$id = $student->student_id;

$controller = app()->make(App\Http\Controllers\StudentController::class);
$req = Request::create('/students/'.$id, 'POST', []);
// set method override
$req->headers->set('X-HTTP-Method-Override', 'DELETE');
$res = $controller->destroy($id);
if(method_exists($res, 'getContent')) echo $res->getContent(); else var_dump($res);

// show the student row after deletion
$after = DB::table('tblstudent')->where('student_id', $id)->first();
echo "\n\n-- student after delete --\n"; print_r($after);

// tail logs
$log = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($log)) { $lines = array_slice(file($log), -40); echo "\n---- last log lines ----\n" . implode('', $lines); }
