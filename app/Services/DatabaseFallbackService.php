<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DatabaseFallbackService
{
    protected $isConnected = false;
    protected $fallbackEnabled;
    protected $storagePath;

    public function __construct()
    {
        $this->fallbackEnabled = config('database_fallback.fallback_enabled', true);
        $this->storagePath = config('database_fallback.storage_path');
        $this->checkConnection();
    }

    public function checkConnection()
    {
        try {
            DB::connection()->getPdo();
            $this->isConnected = true;
        } catch (\Exception $e) {
            $this->isConnected = false;
            Log::warning('Database connection failed: ' . $e->getMessage());
        }

        return $this->isConnected;
    }

    public function isConnected()
    {
        return $this->isConnected;
    }

    public function getData($model, $id = null)
    {
        if ($this->isConnected) {
            return null;
        }

        if (!$this->fallbackEnabled) {
            throw new \Exception('Database is not available and fallback is disabled');
        }

        $cacheKey = "fallback_{$model}" . ($id ? "_{$id}" : '');

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $filePath = $this->storagePath . "/{$model}.json";

        if (Storage::exists($filePath)) {
            $data = json_decode(Storage::get($filePath), true);

            if ($id) {
                $data = collect($data)->firstWhere('id', $id);
            }

            Cache::put($cacheKey, $data, config('database_fallback.cache_duration'));
            return $data;
        }

        return config("database_fallback.static_data.{$model}", []);
    }

    public function saveData($model, $data)
    {
        if ($this->isConnected) {
            return null;
        }

        if (!$this->fallbackEnabled) {
            throw new \Exception('Database is not available and fallback is disabled');
        }

        $filePath = $this->storagePath . "/{$model}.json";

        $existingData = [];
        if (Storage::exists($filePath)) {
            $existingData = json_decode(Storage::get($filePath), true);
        }

        if (isset($data['id'])) {
            $index = array_search($data['id'], array_column($existingData, 'id'));
            if ($index !== false) {
                $existingData[$index] = $data;
            } else {
                $existingData[] = $data;
            }
        } else {
            $data['id'] = count($existingData) + 1;
            $existingData[] = $data;
        }

        Storage::put($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

        Cache::forget("fallback_{$model}");

        return $data;
    }

    public function deleteData($model, $id)
    {
        if ($this->isConnected) {
            return null;
        }

        if (!$this->fallbackEnabled) {
            throw new \Exception('Database is not available and fallback is disabled');
        }

        $filePath = $this->storagePath . "/{$model}.json";

        if (Storage::exists($filePath)) {
            $existingData = json_decode(Storage::get($filePath), true);
            $existingData = array_filter($existingData, function($item) use ($id) {
                return $item['id'] != $id;
            });

            Storage::put($filePath, json_encode(array_values($existingData), JSON_PRETTY_PRINT));
            Cache::forget("fallback_{$model}");

            return true;
        }

        return false;
    }

    public function syncToDatabase()
    {
        if (!$this->isConnected || !$this->fallbackEnabled) {
            return false;
        }

        $files = Storage::files($this->storagePath);

        foreach ($files as $file) {
            $model = str_replace('.json', '', basename($file));
            $data = json_decode(Storage::get($file), true);

            Log::info("Syncing {$model} data to database", ['count' => count($data)]);
        }

        return true;
    }
}