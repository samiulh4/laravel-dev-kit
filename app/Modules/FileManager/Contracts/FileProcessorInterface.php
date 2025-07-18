<?php

namespace App\Modules\FileManager\Contracts;

use App\Modules\FileManager\Models\FileUpload;

interface FileProcessorInterface
{
    /**
     * Process an uploaded file.
     *
     * @param FileUpload $fileUpload
     * @return void
     */
    public function process(FileUpload $fileUpload);
}
