<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable  implements FilamentUser, HasName
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
        'full_name',
        'email',
        'role',
        'branch_store_id',
        'password',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && $this->hasApplicationAccess('gym_landing');
    }

    public function getFilamentName(): string
    {
        // sesuaikan: name / full_name / email
        return (string) ($this->name ?? $this->full_name ?? $this->email ?? 'User');
    }

    public function isAdmin(): bool
    {
        return strtoupper((string) $this->role) === 'ADMIN';
    }

    public function applicationAccesses()
    {
        return $this->hasMany(UserApplicationAccess::class);
    }

    public function hasApplicationAccess(string $applicationCode): bool
    {
        if (!Schema::hasTable('user_application_access')) {
            return true;
        }

        return $this->applicationAccesses()
            ->where('application_code', $applicationCode)
            ->where('is_active', true)
            ->exists();
    }
}
