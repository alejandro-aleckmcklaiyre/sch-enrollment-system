<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $instructor = Auth::user()->instructor;
        
        return view('faculty.profile', ['instructor' => $instructor, 'needsCreation' => !$instructor]);
    }

    public function update(Request $request)
    {
        $instructor = Auth::user()->instructor;
        
        // If instructor doesn't exist, create it
        if (!$instructor) {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'nullable|email',
            ]);

            $instructor = \App\Models\Instructor::create([
                'user_id' => Auth::id(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? Auth::user()->email,
            ]);

            return redirect('/faculty/dashboard')->with('success', 'Profile created successfully');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:tblinstructor,email,' . $instructor->instructor_id . ',instructor_id',
        ]);

        $instructor->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
