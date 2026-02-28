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
        'assigned_storage_location_id',
        'profile_image',
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
     * Get the employee record associated with this user.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'email', 'email');
    }

    /**
     * Get the user's position from their employee record.
     */
    public function getPositionAttribute()
    {
        return $this->employee()?->first()?->position ?? 'User';
    }

    /**
     * Get all task assignments for this user.
     */
    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    /**
     * Get tasks assigned by this user (admin).
     */
    public function assignedTasks()
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_by');
    }

    /**
     * Get the storage location this supervisor is assigned to.
     */
    public function assignedStorageLocation()
    {
        return $this->belongsTo(StorageUnit::class, 'assigned_storage_location_id');
    }

    /**
     * Get all production workers supervised by this user (if supervisor).
     */
    public function supervisedEmployees()
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    /**
     * Get attendance records marked by this supervisor.
     */
    public function markedAttendance()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    /**
     * Check if user is a storage supervisor.
     */
    public function isStorageSupervisor()
    {
        return $this->role === 'storage_supervisor';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
