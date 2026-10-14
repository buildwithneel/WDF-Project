<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents a registered passenger or admin.
 * Extends Laravel's built-in Authenticatable for auth support.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass-assignable fields (safe to fill via create/update)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Fields hidden from array/JSON serialization (never expose password in API responses)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting: automatically converts 'email_verified_at' to Carbon and
     * hashes 'password' when set.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Check if this user is an admin.
     * Used in Blade: @if(auth()->user()->isAdmin())
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relationship: User has many Bookings (one person can book multiple trips)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
