<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an action in the system audit trail.
     */
    public static function log(
        string $action,
        ?Model $entity = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): ActivityLog {
        $userId = $user ? $user->id : Auth::id();

        // Sanitize sensitive fields
        $sanitizedOld = self::sanitize($oldValues);
        $sanitizedNew = self::sanitize($newValues);

        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity ? $entity->getKey() : null,
            'description' => $description,
            'old_values' => $sanitizedOld,
            'new_values' => $sanitizedNew,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    protected static function sanitize(?array $data): ?array
    {
        if (!$data) {
            return null;
        }

        $sensitive = ['password', 'password_confirmation', 'token', 'remember_token', 'secret', 'key'];

        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '********';
            }
        }

        return $data;
    }
}
