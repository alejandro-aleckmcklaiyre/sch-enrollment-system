<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;

        return view('faculty.reports', ['instructor' => $instructor]);
    }
}
