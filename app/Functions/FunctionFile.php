<?php

namespace App\Functions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;
use DOMDocument;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;


class FunctionFile
{
    public static function downloadFileFromTemplate($filePath): array
    {
        $downloaded = 0;
        $templatePath = '/sneat-bootstrap-html-admin-template/';
        $tagsToCheck = [
            'link' => 'href',
            'script' => 'src',
            'img' => 'src'
        ];

        try {
            $html = file_get_contents(public_path($filePath));

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();

            foreach ($tagsToCheck as $tag => $attr) {

                $elements = $dom->getElementsByTagName($tag);
                
                foreach ($elements as $element) {
                    $url = $element->getAttribute($attr);

                    if (Str::startsWith($url, ['http://', 'https://'])) {
                        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $validExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];

                        // Optional: skip unknown types
                        if (!in_array(strtolower($extension), $validExtensions)) {
                            continue;
                        }

                        try {
                            $fileContent = file_get_contents($url);
                            $parsedUrl = parse_url($url);
                            $relativePath = $parsedUrl['path'] ?? '';

                            if (Str::contains($relativePath, $templatePath)) {
                                $relativePath = str_replace($templatePath, '', $relativePath);
                            }

                            $localPath = public_path('uploads/' . ltrim($relativePath, '/'));

                            File::ensureDirectoryExists(dirname($localPath));
                            file_put_contents($localPath, $fileContent);
                            $downloaded++;
                        } catch (Exception $e) {
                            Log::warning("Failed to download $url: " . $e->getMessage());
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'status' => true,
            'message' => "Downloaded {$downloaded} file(s) from link, script, and img tags."
        ];
    } // End of function downloadFileFromTemplate()

    /**
     * Handle file upload
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string|null
     */
    public static function uploadFile(UploadedFile $file, string $directory): string
    {
        try {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $directory . '/' . $fileName;
            $fullDirectory = public_path($directory);
            if (!is_dir($fullDirectory)) {
                mkdir($fullDirectory, 0755, true);
            }
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory, 0755, true);
            }
            $file->move($fullDirectory, $fileName);
            return $filePath;
        } catch (Exception $e) {
            Log::error('Exception occurred [FUPF-100]', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return '';
        }
    }

    /**
     * Handle file (image) upload
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string|null
     */
    public static function uploadImageFile(UploadedFile $file, string $directory, int $maxWidth = 300, int $maxHeight = 200): string
    {
        try {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $directory . '/' . $fileName;
            $fullDirectory = public_path($directory);
            if (!is_dir($fullDirectory)) {
                mkdir($fullDirectory, 0755, true);
            }
            $image = Image::read($file);

            $image->scale($maxWidth, $maxHeight);

            $image->save($fullDirectory . '/' . $fileName);

            return $filePath;
        } catch (Exception $e) {
            Log::error('Exception occurred [FFUI-101]', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return '';
        }
    }

} // End of class FunctionFile.php
