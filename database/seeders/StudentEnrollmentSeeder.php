<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Term;

class StudentEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create the current term
        $currentTerm = Term::getCurrentTerm();
        
        if (!$currentTerm) {
            $currentTerm = Term::orderBy('term_id')->first();
        }

        if (!$currentTerm) {
            $this->command->info('No terms found in database');
            return;
        }

        // Get some students and sections for the current term
        $students = Student::where('is_deleted', 0)->limit(5)->get();
        $sections = Section::where('term_id', $currentTerm->term_id)
            ->where('is_deleted', 0)
            ->get();

        if ($sections->isEmpty()) {
            $this->command->info('No sections found for term: ' . $currentTerm->term_code);
            return;
        }

        $enrollmentCount = 0;
        foreach ($students as $student) {
            // Enroll student in 2-3 random sections
            $randomSections = $sections->random(min(3, $sections->count()));
            
            foreach ($randomSections as $section) {
                // Check if not already enrolled
                $exists = Enrollment::where('student_id', $student->student_id)
                    ->where('section_id', $section->section_id)
                    ->where('is_deleted', 0)
                    ->exists();

                if (!$exists) {
                    Enrollment::create([
                        'student_id' => $student->student_id,
                        'section_id' => $section->section_id,
                        'course_id' => $section->course_id,
                        'date_enrolled' => now(),
                        'status' => 'ENROLLED',
                        'is_deleted' => 0
                    ]);
                    $enrollmentCount++;
                }
            }
        }

        $this->command->info("Created {$enrollmentCount} new enrollments for test students");
    }
}
