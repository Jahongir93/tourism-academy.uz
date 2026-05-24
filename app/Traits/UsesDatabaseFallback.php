<?php

namespace App\Traits;

use App\Services\DatabaseFallbackService;

trait UsesDatabaseFallback
{
    protected function getFallbackService()
    {
        return app(DatabaseFallbackService::class);
    }

    protected function checkDatabaseAndExecute($callback, $fallbackCallback = null)
    {
        $fallbackService = $this->getFallbackService();

        if ($fallbackService->isConnected()) {
            return $callback();
        }

        if ($fallbackCallback) {
            return $fallbackCallback($fallbackService);
        }

        return response()->json([
            'error' => 'Database is not available and no fallback provided'
        ], 503);
    }

    protected function getModelData($modelName, $id = null)
    {
        $fallbackService = $this->getFallbackService();

        if ($fallbackService->isConnected()) {
            $modelClass = "App\\Models\\" . $modelName;
            if ($id) {
                return $modelClass::find($id);
            }
            return $modelClass::all();
        }

        return $fallbackService->getData(strtolower($modelName), $id);
    }

    protected function saveModelData($modelName, $data)
    {
        $fallbackService = $this->getFallbackService();

        if ($fallbackService->isConnected()) {
            $modelClass = "App\\Models\\" . $modelName;
            if (isset($data['id'])) {
                $model = $modelClass::find($data['id']);
                if ($model) {
                    $model->update($data);
                    return $model;
                }
            }
            return $modelClass::create($data);
        }

        return $fallbackService->saveData(strtolower($modelName), $data);
    }
}