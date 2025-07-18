<?php

namespace App\Modules\FileManager\Factories;


use App\Modules\FileManager\Contracts\FileProcessorInterface;
use App\Modules\FileManager\Models\FileProcessor;
use Illuminate\Support\Facades\Log;

class FileProcessorFactory
{
    public static function make(string $processorKey): ?FileProcessorInterface
    {
        $processor = FileProcessor::where('processor_key', $processorKey)
            ->where('is_active', 1)
            ->first();

        if (!$processor) {
            Log::warning("No processor found for: {$processorKey}");
            return null;
        }

        $class = $processor->processor_class;

        if (class_exists($class)) {
            return new $class;
        }

        Log::error("Processor class not found: {$class}");
        return null;
    }
}
