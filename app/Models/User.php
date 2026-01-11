<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'username',
        'email',
        'photo',
        'password',
        'role_id',
    ];

    protected $guarded = ['id'];

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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name === 'superadmin';
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) return false;

        if (is_array($roles)) {
            return in_array($this->role->name, $roles);
        }

        return $this->role->name === $roles;
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) return true; // superadmin bisa semua

        if (!$this->role) return false;

        return $this->role->permissions->contains('name', $permissionName);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }

        return asset('template/dist/img/user2-160x160.jpg');
    }
}
