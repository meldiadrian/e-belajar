<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'completed_lessons',
        'total_lessons',
        'progress_percentage',
        'started_at',
        'completed_at',
        'last_accessed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'completed_lessons' => 'integer',
            'total_lessons' => 'integer',
            'progress_percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
