<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class BranchStore extends Model
{
    use HasFactory;

    protected $appends = [
        'logo_url',
        'favicon_url',
    ];

    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'phone',
        'email',
        'logo'
    ];    

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->admin_logo) {
            return $this->resolveMasterMediaUrl($this->admin_logo);
        }

        if (!$this->logo) {
            return null;
        }

        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        return Storage::disk('public')->url(
            Str::after(ltrim($this->logo, '/'), 'storage/')
        );
    }

    public function getFaviconUrlAttribute(): string
    {
        return $this->logo_url
            ?? config('services.level_fit_master.url').'/admingym/images/gym/fav-icon.png';
    }

    private function resolveMasterMediaUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (!Str::startsWith($path, 'storage/')) {
            $path = 'storage/'.$path;
        }

        return config('services.level_fit_master.url').'/'.$path;
    }

    public function classSession()
    {
        return $this->hasMany(ClassSession::class);    
    }

    public function classSchedule()
    {
        return $this->hasMany(ClassSchedule::class);    
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty("name")) {
                $model->slug = Str::slug($model->name);
            }

            $fields = ['logo'];

            foreach ($fields as $field) {
                if ($model->isDirty($field)) {
                    $oldFile = $model->getOriginal($field);

                    if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            $fields = ['logo'];

            foreach ($fields as $field) {
                if ($model->$field && Storage::disk('public')->exists($model->$field)) {
                    Storage::disk('public')->delete($model->$field);
                }
            }
        });
    }    
}
