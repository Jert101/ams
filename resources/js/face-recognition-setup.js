/**
 * This script helps set up the Face API models.
 * Run this script to download the required models if they don't exist.
 */

const fs = require('fs');
const path = require('path');
const https = require('https');

const modelsDir = path.join(__dirname, '../../public/models');
const baseUrl = 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights';

// Models to download
const models = [
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
];

// Create models directory if it doesn't exist
if (!fs.existsSync(modelsDir)) {
    console.log(`Creating directory: ${modelsDir}`);
    fs.mkdirSync(modelsDir, { recursive: true });
}

// Download each model
models.forEach(model => {
    const url = `${baseUrl}/${model}`;
    const filePath = path.join(modelsDir, model);
    
    // Skip if file already exists
    if (fs.existsSync(filePath)) {
        console.log(`File already exists: ${filePath}`);
        return;
    }
    
    console.log(`Downloading: ${url}`);
    
    const file = fs.createWriteStream(filePath);
    https.get(url, response => {
        response.pipe(file);
        
        file.on('finish', () => {
            file.close();
            console.log(`Downloaded: ${filePath}`);
        });
    }).on('error', err => {
        fs.unlink(filePath, () => {}); // Delete the file if there was an error
        console.error(`Error downloading ${model}: ${err.message}`);
    });
}); 