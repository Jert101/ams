<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CheckFaceModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:face-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if face-api.js models exist';

    /**
     * The path where models should be stored
     */
    protected $modelsPath = 'public/models/face-api/';

    /**
     * Required models
     */
    protected $requiredModels = [
        'tiny_face_detector_model-weights_manifest.json',
        'tiny_face_detector_model.bin',
        'face_landmark_68_model-weights_manifest.json',
        'face_landmark_68_model.bin',
        'face_recognition_model-weights_manifest.json',
        'face_recognition_model.bin',
        'face_expression_model-weights_manifest.json',
        'face_expression_model.bin',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking Face API models...');
        
        $missingModels = [];
        
        foreach ($this->requiredModels as $model) {
            $path = $this->modelsPath . $model;
            
            if (!File::exists($path)) {
                $missingModels[] = $model;
            }
        }
        
        if (empty($missingModels)) {
            $this->info('All face-api.js models are present!');
            return Command::SUCCESS;
        } else {
            $this->error('The following models are missing:');
            foreach ($missingModels as $model) {
                $this->line(" - {$model}");
            }
            
            if ($this->confirm('Do you want to download the missing models now?')) {
                $this->call('face-api:download-models');
            } else {
                $this->info('You can download the models manually using: php artisan face-api:download-models');
            }
            
            return Command::FAILURE;
        }
    }
} 