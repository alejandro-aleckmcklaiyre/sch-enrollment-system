<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is faculty/instructor
     */
    public function isFaculty(): bool
    {
        return $this->role === 'faculty';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Relationship: User has one Instructor
     */
    public function instructor()
    {
        return $this->hasOne(\App\Models\Instructor::class, 'user_id');
    }

    /**
     * Relationship: User has one Student
     */
    public function student()
    {
        return $this->hasOne(\App\Models\Student::class, 'user_id');
    }
}
