<?php

if (!function_exists('vite_assets')) {
    /**
     * Get the path to a versioned Vite file in the shared hosting environment.
     *
     * @param  string  $path
     * @return string
     */
    function vite_assets($path)
    {
        // Check if we're in a production environment by looking for the manifest
        $manifest = file_exists(public_path('build/manifest.json')) 
            ? json_decode(file_get_contents(public_path('build/manifest.json')), true) 
            : null;
            
        if ($manifest && isset($manifest[$path])) {
            return asset('build/' . $manifest[$path]['file']);
        }
        
        // Fallback to direct asset access
        return asset($path);
    }
} 