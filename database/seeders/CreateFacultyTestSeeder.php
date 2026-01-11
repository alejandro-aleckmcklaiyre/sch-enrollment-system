<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Section;
use App\Models\Course;
use App\Models\Term;
use App\Models\Room;
use App\Models\Department;

class CreateFacultyTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the faculty user
        $user = User::firstOrCreate(
            ['email' => 'faculty@faculty.com'],
            [
                'name' => 'Test Faculty',
                'password' => bcrypt('password'),
                'role' => 'faculty',
            ]
        );

        // Create or get the instructor profile
        $instructor = Instructor::firstOrCreate(
            ['email' => 'faculty@faculty.com'],
            [
                'instructor_id' => 99,
                'first_name' => 'Test',
                'last_name' => 'Faculty',
                'dept_id' => Department::first()?->dept_id ?? 1,
            ]
        );

        // Get a term (or create one if needed)
        $term = Term::first();
        if (!$term) {
            $term = Term::create([
                'term_name' => 'Spring 2024',
                'start_date' => '2024-01-15',
                'end_date' => '2024-05-15',
                'is_active' => 1,
            ]);
        }

        // Get some courses (use existing ones)
        $courses = Course::limit(3)->get();
        if ($courses->count() == 0) {
            // No courses available - skip section creation
            $courses = collect([]);
        }

        // Get or create rooms
        $rooms = Room::limit(3)->get();
        if ($rooms->count() < 3) {
            for ($i = 1; $i <= 3; $i++) {
                Room::firstOrCreate(
                    ['room_code' => 'ROOM' . (100 + $i)],
                    [
                        'room_capacity' => 30 + ($i * 10),
                        'has_projector' => true,
                        'has_ac' => true,
                    ]
                );
            }
            $rooms = Room::limit(3)->get();
        }

        // Create sections for this instructor (only if courses exist)
        if ($courses->count() > 0) {
            $scheduleData = [
                ['days' => 'MWF', 'startTime' => '09:00', 'endTime' => '10:30'],
                ['days' => 'TTh', 'startTime' => '10:00', 'endTime' => '11:30'],
                ['days' => 'MWF', 'startTime' => '14:00', 'endTime' => '15:30'],
            ];

            foreach ($courses->take(3) as $index => $course) {
                $schedule = $scheduleData[$index] ?? ['days' => 'MWF', 'startTime' => '09:00', 'endTime' => '10:30'];
                $room = $rooms->get($index % $rooms->count());

                Section::firstOrCreate(
                    [
                        'course_id' => $course->course_id,
                        'term_id' => $term->term_id,
                        'section_code' => $course->course_code . '-' . (($index + 1) * 100),
                        'instructor_id' => $instructor->instructor_id,
                    ],
                    [
                        'section_id' => 500 + $index,
                        'room_id' => $room->room_id,
                        'day_pattern' => $schedule['days'],
                        'start_time' => $schedule['startTime'],
                        'end_time' => $schedule['endTime'],
                        'max_capacity' => $room->room_capacity,
                        'is_deleted' => 0,
                    ]
                );
            }
        }

        $this->command->info('Test faculty user created successfully!');
        $this->command->info('Email: faculty@faculty.com');
        $this->command->info('Password: password');
        $this->command->info('Sections and courses have been assigned to this faculty.');
    }
}
