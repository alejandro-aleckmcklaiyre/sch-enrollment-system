<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $student = Auth::user()->student;
        
        return view('student.profile', ['student' => $student, 'needsCreation' => !$student]);
    }

    public function update(Request $request)
    {
        $student = Auth::user()->student;
        
        // If student doesn't exist, create it
        if (!$student) {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'nullable|email',
            ]);

            $student = \App\Models\Student::create([
                'user_id' => Auth::id(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? Auth::user()->email,
            ]);

            return redirect('/student/dashboard')->with('success', 'Profile created successfully');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:tblstudent,email,' . $student->student_id . ',student_id',
        ]);

        $student->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
