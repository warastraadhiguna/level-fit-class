<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'class_instructor_id',
        'branch_store_id',
        'name',
        'note',
        'capacity',              
        'price',
		'day', 
		'time_start',
		'time_end',		
        'is_active'
    ];

    public function classInstructor()
    {
        return $this->belongsTo(ClassInstructor::class);
    }    

    public function branchStore()
    {
        return $this->belongsTo(BranchStore::class);
    }       
}
