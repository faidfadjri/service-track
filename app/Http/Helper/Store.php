<?php

namespace App\Http\Helper;

use Exception;

class Store
{
    public static function insert(string $modelClass, array $data)
    {
        try {
            $model = new $modelClass;
            $model->fill($data); 
            $model->save();
            $modelName = class_basename($model);
            $successMessage = "{$modelName} Created successfully";
            return response()->json(['message' => $successMessage]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(string $modelClass, array $data, int $id)
    {
        try {
            $model = $modelClass::find($id);
            
            if (!$model) {
                return response()->json(['message' => 'Record not found'], 404);
            }
    
            $model->fill($data);
            $model->save();
    
            $modelName = class_basename($model);
            $successMessage = "{$modelName} updated successfully";
            return response()->json(['message' => $successMessage]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
}
