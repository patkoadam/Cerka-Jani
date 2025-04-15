<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_group_id', 'teacher_id', 'subject', 'day_of_week', 'start_time', 'end_time', 'room'
    ];

    // Az órarend melyik osztálycsoporthoz tartozik
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    // A tanár, aki ezt az órarendet létrehozta
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

