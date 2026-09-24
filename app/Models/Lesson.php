<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'title',
        'slug',
        'description',
        'lesson_type',
        'content',
        'duration',
        'sort_order',
        'is_preview',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'duration' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(LessonContent::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
