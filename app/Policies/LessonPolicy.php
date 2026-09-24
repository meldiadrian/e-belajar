<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Lesson $lesson): bool
    {
        if ($lesson->is_preview) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $courseId = $lesson->module->course_id;

        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin();
    }
}
