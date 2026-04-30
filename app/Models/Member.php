<?php

namespace App\Models;

use App\Models\User; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'branch_store_id',
        'full_name',
        'nickname',
        'member_code',
        'card_number',
        'gender',
        'born',
        'phone_number',
        'email',
        'ig',
        'emergency_contact',
        'ec_name',
        'address',
        'status',
        'description',
        'photos',
        'fc_candidate_id',
        'cancellation_note',
        'status',
        'lo_is_used',
        'lo_start_date',
        'lo_days',
        'lo_pt_by',
        'lo_end',
        'google_id', 
        'avatar',
        'last_login_at'
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\ClassDetail::class);
    }    
}