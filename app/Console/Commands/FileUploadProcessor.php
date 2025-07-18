<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\FileManager\Models\FileUpload;
use Illuminate\Support\Facades\Log;
use App\Modules\FileManager\Factories\FileProcessorFactory;

class FileUploadProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:upload-processor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process uploaded files by their processor key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileUpload = FileUpload::where('file_status', 'pending')->first();
        if ($fileUpload) {
            if (!file_exists($fileUpload->file_path)) {
                //$fileUpload->file_status = 'failed';
                $fileUpload->process_message = 'File not found on disk: ' . $fileUpload->file_path;
                $fileUpload->save();

                $this->error("File not found on disk: {$fileUpload->file_path}");
                return;
            }

            $factory = FileProcessorFactory::make($fileUpload->processor_key);

            if ($factory) {
                try {
                    $factory->process($fileUpload);

                    //$fileUpload->file_status = 'processing';
                    $fileUpload->process_message = 'File processed successfully.';
                    $fileUpload->save();

                    $this->info("Processed file ID {$fileUpload->id} with key {$fileUpload->processor_key}");
                } catch (\Throwable $e) {
                    //$fileUpload->file_status = 'failed';
                    $fileUpload->process_message = $e->getMessage();
                    $fileUpload->save();

                    $this->error("Failed to process file ID {$fileUpload->id}. Error: " . $e->getMessage());
                }
            } else {
                //$fileUpload->file_status = 'failed';
                $fileUpload->process_message = 'No processor found for key: ' . $fileUpload->processor_key;
                $fileUpload->save();

                $this->warn("No processor found for key: {$fileUpload->processor_key}");
            }
        } else {
            $this->warn("No file processor found!");
        }
    }
}
