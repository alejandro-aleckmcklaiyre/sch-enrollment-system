<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\Section;
use App\Models\Term;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;

class CheckData extends Command
{
    protected $signature = 'app:check-data';
    protected $description = 'Check database data';

    public function handle()
    {
        $this->info("\n========== DATABASE DATA CHECK ==========\n");

        $this->line('Courses: ' . Course::where('is_deleted', 0)->count());
        $this->line('Sections: ' . Section::where('is_deleted', 0)->count());
        $this->line('Terms: ' . Term::where('is_deleted', 0)->count());
        $this->line('Students: ' . Student::where('is_deleted', 0)->count());
        $this->line('Instructors: ' . Instructor::where('is_deleted', 0)->count());
        $this->line('Enrollments: ' . Enrollment::where('is_deleted', 0)->count());

        $this->line("\n--- Current Term ---");
        $term = Term::getCurrentTerm();
        if ($term) {
            $this->line("Current: {$term->term_code} - {$term->term_name}");
        } else {
            $this->line("No current term set");
        }

        $this->line("\n--- Active Sections with Current Term ---");
        $currentTerm = Term::getCurrentTerm();
        if ($currentTerm) {
            $sections = Section::where('term_id', $currentTerm->term_id)
                ->where('is_deleted', 0)
                ->limit(5)
                ->get();
            foreach ($sections as $section) {
                $this->line("Section {$section->section_id}: Course {$section->course_id}, Max: {$section->max_capacity}");
            }
        }

        $this->info("\n==========================================\n");
    }
}
