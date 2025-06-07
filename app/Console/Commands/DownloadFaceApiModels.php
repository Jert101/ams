<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadFaceApiModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face-api:download-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download face-api.js models from CDN';

    /**
     * Base URL for face-api models
     */
    protected $baseUrl = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.2/model/';

    /**
     * Models to download
     */
    protected $models = [
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
     * The path where models will be saved
     */
    protected $savePath = 'public/models/face-api/';

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
        $this->info('Downloading Face API models...');
        
        // Create directory if it doesn't exist
        if (!File::exists($this->savePath)) {
            File::makeDirectory($this->savePath, 0755, true);
        }
        
        $bar = $this->output->createProgressBar(count($this->models));
        $bar->start();
        
        foreach ($this->models as $model) {
            $url = $this->baseUrl . $model;
            $savePath = $this->savePath . $model;
            
            try {
                $response = Http::get($url);
                
                if ($response->successful()) {
                    File::put($savePath, $response->body());
                    $this->line(" Downloaded model: $model");
                } else {
                    $this->line(" Failed to download model: $model");
                    $this->line(" Status code: " . $response->status());
                }
            } catch (\Exception $e) {
                $this->line(" Error downloading model: $model");
                $this->line(" Error: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Face API models downloaded successfully!');
        
        return Command::SUCCESS;
    }
} 