<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class CleanCommand extends Command
{
    protected $signature = 'clean {file}';

    protected $description = 'Clean the given lottie JSON file.';

    public function handle()
    {
        $path = getcwd() . '/' . $this->argument('file');

        if (File::isFile($path)) {
            $this->handleFile($path);
        }

        if (File::isDirectory($path)) {
            foreach (File::allFiles($path) as $file) {
                $this->handleFile($file->getPathname());
            }
        }

        $this->info('Done! 🚀');
    }

    public function handleFile($path)
    {
        $content = File::get($path);
        $cleaned = $this->clean(['nm', 'mn'], json_decode($content, true));
        File::put($path, json_encode($cleaned));
    }

    protected function clean($keys, $array = null)
    {
        $keys = is_array($keys) ? $keys : [$keys];

        $array = $array ?? $this->structure;

        return collect($array)->except($keys)->map(function ($item) use ($keys) {
            if (is_array($item)) {
                return $this->clean($keys, $item);
            }

            return $item;
        })->toArray();
    }
}
