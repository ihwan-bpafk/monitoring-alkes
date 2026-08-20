<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void created(\Closure|string|array $callback)
 * @method static void updated(\Closure|string|array $callback)
 * @method static void deleted(\Closure|string|array $callback)
 */
trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logAction('Create', $model);
        });

        static::updated(function ($model) {
            self::logAction('Update', $model);
        });

        static::deleted(function ($model) {
            self::logAction('Delete', $model);
        });
    }

    public static function logAction(string $action, \Illuminate\Database\Eloquent\Model $model)
    {
        \Illuminate\Support\Facades\Log::info("LogsActivity triggered for {$action} on " . class_basename($model));
        if (Auth::check()) {
            $before = null;
            $after = null;

            if ($action === 'Update') {
                $before = $model->getOriginal();
                $after = $model->getChanges();
                
                // Exclude timestamps
                unset($before['updated_at']);
                unset($after['updated_at']);
                
                // Keep only the attributes that changed in the 'before' array
                $before = array_intersect_key($before, $after);
            } elseif ($action === 'Create') {
                $after = $model->getAttributes();
            } elseif ($action === 'Delete') {
                $before = $model->getAttributes();
            }

            $ip = request()->ip();
            $location = self::getLocationFromIp($ip);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => class_basename($model),
                'model_id' => $model->id ?? null,
                'description' => "$action data " . class_basename($model),
                'before' => $before ? $before : null,
                'after' => $after ? $after : null,
                'ip_address' => $ip,
                'location' => $location,
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    public static function getLocationFromIp(string|null $ip)
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return 'Localhost';
        }

        // Cache location for 24 hours to avoid API limits
        return Cache::remember("ip_location_{$ip}", 86400, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json('city') . ', ' . $response->json('country');
                }
            } catch (\Exception $e) {
                // Return unknown if API fails
            }
            return 'Unknown';
        });
    }
}
