<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stage extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function curricula()
    {
        return $this->belongsToMany(Curriculum::class, 'stage_curriculum');
    }


    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'stage_subject');
    }


}

