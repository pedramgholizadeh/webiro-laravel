<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportStatic extends Command
{
    protected $signature = 'static:export';

    protected $description = 'Export Laravel pages to static HTML';

    public function handle(): int
    {
        $outputPath = base_path('dist');

        /*
        |--------------------------------------------------------------------------
        | Clean dist
        |--------------------------------------------------------------------------
        */

        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        File::makeDirectory($outputPath, 0755, true);

        /*
        |--------------------------------------------------------------------------
        | Export Homepage
        |--------------------------------------------------------------------------
        */

        $this->exportPage('/', $outputPath);

        /*
        |--------------------------------------------------------------------------
        | Export Database Pages
        |--------------------------------------------------------------------------
        */

        $pages = Page::query()
            ->get();

        foreach ($pages as $page) {

            $slug = trim($page->slug, '/');

            if ($slug === '') {
                continue;
            }

            $this->exportPage(
                '/' . $slug,
                $outputPath
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Copy Vite Build
        |--------------------------------------------------------------------------
        */

        $buildPath = public_path('build');

        if (File::exists($buildPath)) {

            File::copyDirectory(
                $buildPath,
                $outputPath . '/build'
            );

            $this->info('Copied: /build');
        }

        /*
        |--------------------------------------------------------------------------
        | Copy Public Files
        |--------------------------------------------------------------------------
        */

        foreach (File::files(public_path()) as $file) {

            if ($file->getFilename() === 'index.php') {
                continue;
            }

            File::copy(
                $file->getPathname(),
                $outputPath . '/' . $file->getFilename()
            );
        }

        $this->newLine();

        $this->info(
            'Static export completed successfully.'
        );

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | Export Individual Page
    |--------------------------------------------------------------------------
    */

    private function exportPage(
        string $url,
        string $outputPath
    ): void {

        try {

            $request = \Illuminate\Http\Request::create(
                $url,
                'GET'
            );

            $response = app()->handle($request);

            if ($response->getStatusCode() !== 200) {

                $this->warn(
                    "Skipped: {$url} (HTTP {$response->getStatusCode()})"
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Determine target
            |--------------------------------------------------------------------------
            */

            if ($url === '/') {

                $target = $outputPath . '/index.html';

            } else {

                $target =
                    $outputPath .
                    '/' .
                    trim($url, '/') .
                    '/index.html';
            }

            /*
            |--------------------------------------------------------------------------
            | Create directory
            |--------------------------------------------------------------------------
            */

            $directory = dirname($target);

            if (! File::exists($directory)) {

                File::makeDirectory(
                    $directory,
                    0755,
                    true
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Write HTML
            |--------------------------------------------------------------------------
            */

            File::put(
                $target,
                $response->getContent()
            );

            $this->info(
                "Exported: {$url}"
            );

        } catch (\Throwable $e) {

            $this->error(
                "Failed: {$url}"
            );

            $this->error(
                $e->getMessage()
            );
        }
    }
}