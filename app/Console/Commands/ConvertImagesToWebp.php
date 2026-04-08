<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp {path=public/test-images}';
    protected $description = 'TEST: Convert images to WebP';

    public function handle()
    {
        // ✅ Safety check
        if ($this->argument('path') === 'public/images') {
            $this->error('❌ Live folder blocked. Use test folder first.');
            return;
        }

        if (!function_exists('imagewebp')) {
            $this->error('❌ WebP not supported by GD.');
            return;
        }

        $path = base_path($this->argument('path'));
        $manager = new ImageManager(new Driver());

        foreach (File::files($path) as $file) {
            if (!in_array($file->getExtension(), ['jpg', 'jpeg', 'png'])) {
                continue;
            }

            $webpPath = $file->getPath() . '/' .
                pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.webp';

            $manager
                ->read($file->getRealPath())
                ->toWebp(80)
                ->save($webpPath);

            $this->info("✅ Converted: {$file->getFilename()}");
        }

        $this->info('🎉 Test completed safely!');
    }
}
