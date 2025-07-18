<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadCustomizerImages extends Command
{
    protected $signature = 'customizer:download-images';
    // php artisan customizer:download-images
    protected $description = 'Download customizer SVG images from Sneat template';

    public function handle()
    {
        $urls = [
            'skin-default.svg',
            'skin-border.svg',
            'layouts-expanded.svg',
            'layouts-collapsed.svg',
            'content-compact.svg',
            'content-wide.svg',
            'horizontal-fixed.svg',
            'horizontal-static.svg',
            'navbar-sticky.svg',
            'navbar-static.svg',
            'navbar-hidden.svg',
            'direction-ltr.svg',
            'direction-rtl.svg',
        ];

        $baseUrl = 'https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/customizer/';
        $savePath = public_path('assets/admin/img/customizer/');

        if (!File::exists($savePath)) {
            File::makeDirectory($savePath, 0755, true);
        }

        foreach ($urls as $filename) {
            $url = $baseUrl . $filename;
            $this->info("Downloading: $url");

            try {
                $response = Http::timeout(20)->withOptions(['verify' => false])->get($url);
               // $response = Http::timeout(20)->get($url);

                if ($response->successful()) {
                    File::put($savePath . $filename, $response->body());
                    $this->info("Saved: $filename");
                } else {
                    $this->error("Failed to download: $filename (Status: " . $response->status() . ")");
                }
            } catch (\Exception $e) {
                $this->error("Error downloading $filename: " . $e->getMessage());
            }
        }

        $this->info('✔ All images processed.');
    }
}
