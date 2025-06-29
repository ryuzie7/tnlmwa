<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearAssetCache extends Command
{
    protected $signature = 'cache:clear-assets';

    protected $description = 'Clear cached asset data';

    public function handle()
    {
        Cache::forget('asset_index');
        Cache::forget('asset_types');
        Cache::forget('asset_conditions');
        Cache::forget('asset_locations');

        $this->info('Asset-related cache cleared successfully.');
    }
}
