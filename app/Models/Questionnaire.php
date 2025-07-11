<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'questions_and_answers',
    ];

    protected $casts = [
        'questions_and_answers' => 'array',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}