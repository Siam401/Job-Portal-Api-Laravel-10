<?php

namespace App\Console\Commands;

use App\Services\FileUpload\FileUpload;
use Illuminate\Console\Command;
use Modules\User\Models\Applicant;

class FilterFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear or Remove files from storage if no applicants are present';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning upload files...');

        $storagePath = FileUpload::storagePath('');

        if (!Applicant::exists()) {

            $dirs = array_map('basename', glob($storagePath . '/*', GLOB_ONLYDIR));
            // array_filter(glob(FileUpload::storagePath('')), 'is_dir');

            $cnt = 0;
            $directories = [];
            foreach ($dirs as $dirName) {
                if (is_numeric($dirName)) {
                    $directories[] = $dirName;
                    FileUpload::removeDirectory($dirName);
                    $cnt++;
                }
            }

            $this->info('Removed ' . $cnt . ' directories... ' . implode(', ', $directories));
        } else {
            $this->info('Applicants exists! Upload filter operation is terminated...');
        }

    }
}