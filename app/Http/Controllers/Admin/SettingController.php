<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        // Get application settings
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_timezone' => config('app.timezone'),
            'app_locale' => config('app.locale'),
        ];
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|max:2048',
            'app_timezone' => 'required|string|max:255',
            'app_locale' => 'required|string|max:255',
        ]);

        // Update .env file with new settings
        $this->updateEnvFile('APP_NAME', '"' . $validated['app_name'] . '"');
        $this->updateEnvFile('APP_TIMEZONE', $validated['app_timezone']);
        $this->updateEnvFile('APP_LOCALE', $validated['app_locale']);

        // Update runtime configuration immediately
        config(['app.name' => $validated['app_name']]);
        config(['app.timezone' => $validated['app_timezone']]);
        config(['app.locale' => $validated['app_locale']]);

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            // Delete old logo if exists
            if (file_exists(public_path('kofa.png'))) {
                unlink(public_path('kofa.png'));
            }
            
            // Save new logo
            $request->file('app_logo')->move(public_path(), 'kofa.png');
        }

        // Clear all caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Update the .env file with new values.
     */
    private function updateEnvFile($key, $value)
    {
        $path = app()->environmentFilePath();
        $content = file_get_contents($path);

        // If the key exists, replace it
        if (strpos($content, "{$key}=") !== false) {
            $content = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $content);
        } else {
            // If the key doesn't exist, add it
            $content .= "\n{$key}={$value}";
        }

        // Save the updated content back to the .env file
        file_put_contents($path, $content);
        
        // Update the runtime configuration as well
        config([strtolower(str_replace('APP_', 'app.', $key)) => $value]);
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Application cache cleared successfully.');
    }
}
