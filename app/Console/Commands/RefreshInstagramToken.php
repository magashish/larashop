<?php

// app/Console/Commands/RefreshInstagramToken.php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Cache; // ✅ Ye add karo

use App\Services\InstagramTokenService;
use Illuminate\Console\Command;

class RefreshInstagramToken extends Command
{
    protected $signature   = 'instagram:refresh-token';
    protected $description = 'Instagram access token auto refresh karo';

    public function handle(InstagramTokenService $service): void
    {
        $this->info('Token refresh ho raha hai...');

        $success = $service->refreshToken();
        Cache::forget('instagram_posts'); 

        if ($success) {
            $this->info('✅ Token successfully refresh ho gaya!');
        } else {
            $this->error('❌ Token refresh fail ho gaya. Log check karo.');
        }
    }
}
