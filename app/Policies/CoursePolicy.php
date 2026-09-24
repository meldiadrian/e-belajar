<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        if ($course->status === 'published') {
            return true;
        }

        return $user && ($user->isAdmin() || $user->id === $course->created_by);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() && ($user->id === $course->created_by || $user->isSuperAdmin());
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin() && ($user->id === $course->created_by || $user->isSuperAdmin());
    }

    public function enroll(User $user, Course $course): bool
    {
        return $course->status === 'published';
    }
}
