<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class CleanupDeletedPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:delete_trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup deleted photos from the database after 30 minutes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedFolderPath = public_path('deleted-photos');
        if (File::exists($deletedFolderPath)) {
            $deletionTimestamp = File::lastModified($deletedFolderPath);
            $thirtyMinutesAgo = Carbon::now()->subMinutes(2);

            if ($deletionTimestamp <= $thirtyMinutesAgo) {
                File::deleteDirectory($deletedFolderPath);
                $this->info('Deleted photos folder and its contents have been deleted.');
            } else {
                $this->info('Deletion time has not passed yet. The folder will be deleted later.');
            }
        } else {
            $this->info('Deleted photos folder does not exist.');
        }
    }
}