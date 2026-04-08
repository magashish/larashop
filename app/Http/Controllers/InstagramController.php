<?php

// app/Http/Controllers/InstagramController.php

namespace App\Http\Controllers;

use App\Services\InstagramTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class InstagramController extends Controller
{
    public function __construct(protected InstagramTokenService $tokenService) {}

    // Frontend ke liye posts API
    // public function getPosts()
    // {
    //     // 1 ghante ka cache — baar baar API call nahi hogi
    //     $posts = Cache::remember('instagram_posts', 3600, function () {
    //         $token = $this->tokenService->getToken();

    //         if (!$token) {
    //             return [];
    //         }

    //         $response = Http::get('https://graph.instagram.com/me/media', [
    //             'fields'       => 'id,caption,media_url,permalink,media_type,thumbnail_url',
    //             'access_token' => $token,
    //             'limit'        => 100,
    //         ]);

    //         if ($response->failed()) {
    //             return [];
    //         }

    //         return $response->json()['data'] ?? [];
    //     });

    //     return response()->json($posts);
    // }


   public function getPosts()
{
    $token = $this->tokenService->getToken();
    
    if (!$token) {
        return response('');
    }

    $response = Http::get('https://graph.instagram.com/me/media', [
        'fields'       => 'id,caption,media_url,permalink,media_type,thumbnail_url',
        'access_token' => $token,
        'limit'        => 100,
    ]);

    if ($response->failed()) {
        return response('');
    }
    $posts = $response->json()['data'] ?? [];
    if (empty($posts)) {
        return response('');
    }

    // HTML build karo
    $html = '';
    foreach ($posts as $post) {
        if ($post['media_type'] === 'VIDEO') {
            $html .= '
            <div style="padding:5px;">
                <div style="position:relative; background:#000; overflow:hidden; border-radius:15px;">
                    <a href="' . $post['permalink'] . '" target="_blank" rel="noopener" style="display:block;">
                        <video
                            src="' . $post['media_url'] . '"
                            poster="' . ($post['thumbnail_url'] ?? '') . '"
                            muted loop playsinline preload="metadata"
                            style="width:100%; height:300px; object-fit:cover; display:block; border-radius:15px;">
                        </video>
                    </a>
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#fff; font-size:24px; pointer-events:none; opacity:0.8;">▶</div>
                </div>
            </div>';
        } else {
            $html .= '
            <div style="padding:5px;">
                <div style="overflow:hidden; border-radius:15px;">
                    <a href="' . $post['permalink'] . '" target="_blank" rel="noopener">
                        <img
                            src="' . $post['media_url'] . '"
                            alt="' . substr($post['caption'] ?? 'Instagram Post', 0, 50) . '"
                            style="width:100%; height:300px; object-fit:cover; display:block; border-radius:15px;">
                    </a>
                </div>
            </div>';
        }
    }

    return response($html)->header('Content-Type', 'text/html');
}

    // Pehli baar token save karne ke liye (sirf ek baar use karo)
    public function saveToken(Request $request)
    {
        // Sirf local ya admin access
        if (!app()->isLocal() && !$request->user()?->isAdmin()) {
            abort(403);
        }

        $request->validate(['short_token' => 'required|string']);

        $result = $this->tokenService->saveInitialToken($request->short_token);

        return response()->json($result);
    }
}
