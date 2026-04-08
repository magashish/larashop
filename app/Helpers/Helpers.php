<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use App\Models\Offer;
use App\Models\Organisation;
use App\Models\Admin;
use App\Models\User;
use App\Models\AdminLog;
use Carbon\Carbon; // For current date
use App\Models\UserPackageSubscription;
use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry;
use App\Models\GoogleReview;


use App\Models\Plan; // Assuming your Plan model is in App\Models
use App\Enums\OrganisationStatus;   // Make sure you have this enum defined

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;



use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;

use Stripe\Stripe;
use Stripe\Invoice;
use Stripe\Charge;



if (!function_exists('processImageUpload')) {
    function processImageUpload($file, $uploadDirectory, $maxWidth = 1200, $jpegQuality = 85)
    {
        $imageManager = new ImageManager(GdDriver::class); 

        $optimizerChain = OptimizerChainFactory::create();
        // Original filename and extension
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $originalName . '.' . $extension;
        $path = $uploadDirectory . '/' . $filename;
        // Add numbering if file exists
        $counter = 1;
        while (Storage::disk('public')->exists($path)) {
            $filename = $originalName . '-' . $counter . '.' . $extension;
            $path = $uploadDirectory . '/' . $filename;
            $counter++;
        }
        // Handle SVG directly
        if ($extension === 'svg') {
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            return $path;
        }
        // Resize / scale down
        $image = $imageManager->read($file->getRealPath())
            ->scaleDown(width: $maxWidth);

        switch ($extension) {
            case 'png':
                $encoded = $image->toPng();
                break;
            case 'gif':
                $encoded = $image->toGif();
                break;
            default:
                $encoded = $image->toJpeg($jpegQuality);
                break;
        }

        // Save file
        Storage::disk('public')->put($path, (string)$encoded);

        // Optimize
        $absolutePath = Storage::disk('public')->path($path);
        $optimizerChain->optimize($absolutePath);

        return $path;
    }
}


if (!function_exists('reprocessStoredImage')) {
    function reprocessStoredImage($fileName, $sourceDir, $targetDir, $maxWidth = 1200, $jpegQuality = 85)
    {
        $imageManager = new ImageManager(new GdDriver());
        $optimizerChain = OptimizerChainFactory::create();
        $disk = Storage::disk('public');

        $sourcePath = "{$sourceDir}/{$fileName}";
        $targetPath = "{$targetDir}/{$fileName}";

        // Ensure source exists
        if (!$disk->exists($sourcePath)) {
            echo "⚠️ Source not found: {$sourcePath}\n";
            return;
        }

        // ✅ Ensure destination folder exists
        if (!$disk->exists($targetDir)) {
            $disk->makeDirectory($targetDir);
            echo "📁 Created directory: {$targetDir}\n";
        }

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $absoluteSourcePath = $disk->path($sourcePath);
        $absoluteTargetPath = $disk->path($targetPath);

        try {
            if ($extension === 'svg') {
                $disk->put($targetPath, file_get_contents($absoluteSourcePath));
                echo "✅ Copied SVG: {$fileName}\n";
                return;
            }

            // Read & resize
            $image = $imageManager->read($absoluteSourcePath)->scaleDown(width: $maxWidth);

            switch ($extension) {
                case 'png':
                    $encoded = $image->toPng();
                    break;
                case 'gif':
                    $encoded = $image->toGif();
                    break;
                default:
                    $encoded = $image->toJpeg($jpegQuality);
                    break;
            }

            // ✅ Save file
            $disk->put($targetPath, (string) $encoded);

            // ✅ Optimize
            $optimizerChain->optimize($absoluteTargetPath);

            echo "✅ Processed: {$sourcePath} → {$targetPath}\n";
        } catch (\Exception $e) {
            echo "❌ Failed to process {$fileName}: " . $e->getMessage() . "\n";
        }
    }
}



if (!function_exists('admin_user_role')) {
    function admin_user_role($key = null)
    {
        $roles = [
            'admin' => 'Admin Role',
            'editor' => 'Editor Role'
        ];
        if ($key !== null) {
            return $roles[$key] ?? null; 
        }
        return $roles;
    }
}


if (!function_exists('available_roles')) {
    function available_roles($key = null)
    {
        $excludedRoleIds = [10, 12, 13, 17, 14, 15, 16];
        $availableRoles = Role::whereNotIn('id', $excludedRoleIds)
        ->pluck('name', 'id')
        ->toArray();
        return $availableRoles;
    }
}


if (!function_exists('planList')) {
    function planList($key = null)
    {
        $plans = Plan::where('is_public', true)->get();
        return $plans;
    }
}


if (!function_exists('planPackageList')) {
    function planPackageList()
    {
        return [
            'subscriptions' => Plan::where('is_public', true)
                                    ->where('type', 'subscription')
                                    ->get(),   // KEEP AS MODELS

            'packages' => Plan::where('is_public', true)
                               ->where('type', 'package')
                               ->get(),       // KEEP AS MODELS
        ];
    }
}





if (!function_exists('get_offers_count')) {

    function get_offers_count(): int
    {
        return Offer::where('processing_status', \App\Enums\OrganisationStatus::NEW)->count();
    }
}

if (!function_exists('get_organisations_count')) {
    function get_organisations_count(): int
    {
        return Organisation::where('processing_status', \App\Enums\OrganisationStatus::NEW)->count();
    }
}


if (!function_exists('get_total_organisations_count')) {
    function get_total_organisations_count(): int
    {
        return Organisation::count();
    }
}


if (!function_exists('organisations_data')) {
    function organisations_data(): array
    {
        $organisations_new = Organisation::where('processing_status', OrganisationStatus::NEW)->count();
        $organisations_approved = Organisation::where('processing_status', OrganisationStatus::APPROVED)->count();
        $organisations_rejected = Organisation::where('processing_status', OrganisationStatus::REJECTED)->count();

        return [
            'new'      => $organisations_new,
            'approved' => $organisations_approved,
            'rejected' => $organisations_rejected,
            'total'    => Organisation::count(),
        ];
    }
}


if (!function_exists('get_total_offer_count')) {
    function get_total_offer_count(): int
    {
        return Offer::count();
    }
}


if (!function_exists('offer_data')) {
    function offer_data(): array
    {
        $today = Carbon::today();
        $baseQuery = Offer::with(['organisation', 'categories']);

        $offer_new = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::NEW)
        ->count();

        $offer_approved = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->count();

        $offer_rejected = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::REJECTED)
        ->count();

        $offer_expire = (clone $baseQuery)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where('end_date', '<', $today)
        ->count();

        $live_offers = (clone $baseQuery)
        ->where('start_date', '<=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        })
        ->count();

        return [
            'new'      => $offer_new,
            'approved' => $offer_approved,
            'rejected' => $offer_rejected,
            'expired'  => $offer_expire,
            'live'     => $live_offers,
        ];
    }
}




if (!function_exists('get_total_user_count')) {
    function get_total_user_count(): int
    {
        return User::count();
    }
}


if (!function_exists('getDailyActiveConsumers')) {
    function getDailyActiveConsumers(): int
    {
        return  User::whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active')
            ->where('ends_at', '>', Carbon::now());
        })->count();
    }
}


if (!function_exists('getPayingSubscribers')) {
    function getPayingSubscribers(): int
    {
        return User::whereHas('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        })->distinct('id')->count('id');
    }
}


if (!function_exists('getInactiveSubscribersCount')) {

    function getInactiveSubscribersCount(): int
    {
        return User::whereDoesntHave('userPackageSubscriptions', function ($query) {
            $query->where('status', 'active');
        })->count();
    }
}


if (!function_exists('subscription_types')) {
    function subscription_types($key = null)
    {
        $types = [
            'subscription' => 'Subscription Plan',
            'package' => 'Product Package'
        ];
        if ($key !== null) {
            return $types[$key] ?? null; 
        }
        return $types;
    }
}

if (!function_exists('getUniqueFileName')) {
    function getUniqueFileName($originalName, $directory)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueFilename = $originalName;
        $counter = 0;
        while (Storage::disk('public')->exists($directory . '/' . $uniqueFilename)) {
            $counter++;
            $uniqueFilename = $filenameWithoutExt . '-' . $counter . '.' . $extension;
        }
        return $uniqueFilename;
    }
}


if (!function_exists('systemFields')) {
    function systemFields($key = null)
    {
        $systemFields = [
            'title'                     => 'Title (Required)',
            'description'               => 'Description (Optional)',
            'start_date'                => 'Start Date (Optional)',
            'end_date'                  => 'End Date (Optional)',
            'image'                     => 'Image URL (Optional)',
            'custom_redemption_url'     => 'Coupon URL (Required if no Code)',
            'custom_redemption_code'    => 'Coupon Code (Required if no URL)',
            'terms_conditions'          => 'Terms & Conditions (Optional)',
            'full_price'                => 'Full Price (Optional)',
            'discount_price'            => 'Discount Price (Optional)',
            'percentage_off'            => 'Percentage Off (Optional)',
            'custom_saving'             => 'Custom Saving (Optional)',
            'custom_redemption_description' => 'Custom Redemption Description (Optional)',
            'redemption_limit'          => 'Redemption Limit (Optional)',
            'full_address'              => 'Full Address (Optional)',
            'latitude'                  => 'Lat (Optional)',
            'longitude'                 => 'Lng (Optional)',
            'street_number'             => 'Street Number (Optional)',
            'street_name'               => 'Street Name (Optional)',
            'city'                      => 'City (Optional)',
            'state'                     => 'State (Optional)',
            'postcode'                  => 'Postal Code (Optional)',
            'country'                   => 'Country (Optional)',
        ];
        if ($key !== null) {
            return $systemFields[$key] ?? null; 
        }
        return $systemFields;
    }
}



// if (!function_exists('admin_logs_func')) {
//     function admin_logs_func(){
//         //$admin_id = Auth::guard('admin')->id();
//         $admin_id = Auth::id(); 
//         $admin_module ='ping'; 
//         $admin_module_action = null; 
//         $currentUrl = Request::fullUrl();
//         // $currentUrl = Request::getRequestUri(); // This is a more direct PHP way to get /path?query=string
//         AdminLog::create([
//             'admin_id' => $admin_id,
//             'admin_module' => $admin_module,
//             'admin_module_action' => $admin_module_action,
//             'url' => $currentUrl,
//         ]);
//     }
// }



if (!function_exists('events_category')) {
    function events_category($key = null)
    {
        $events_category = [
            '1' => 'CASH GIVEAWAYS',
            '2' => 'PRIZE GIVEAWAYS',
            '3' => 'CHARITY',
            '4' => 'EVENTS CATEGORY'
        ];
        if ($key !== null) {
            return $events_category[$key] ?? null; 
        }
        return $events_category;
    }
}


if (!function_exists('user_total_entries')) {
    function user_total_entries($key = null, $userId = null, $limit = 1, $withBackups = true)
    {
        try {
            // $query = DB::table(function($query) use ($withBackups) {
            //     $query->select('user_id', DB::raw('SUM(entries_added) as total_entries'))
            //         ->from('prize_draw_entries')
            //         ->groupBy('user_id');

            //     if ($withBackups) {
            //         $query->unionAll(
            //             DB::table('prize_draw_entries_backups')
            //                 ->select('user_id', DB::raw('SUM(entries_added) as total_entries'))
            //                 ->groupBy('user_id')
            //         );
            //     }
            // }, 'combined_entries')
            // ->join('users', 'users.id', '=', 'combined_entries.user_id')
            // ->select(
            //     'users.id',
            //     'users.name',
            //     DB::raw('SUM(combined_entries.total_entries) as total_entries')
            // )
            // ->groupBy('users.id', 'users.name')
            // ->orderByDesc('total_entries');


            $query = DB::table('prize_draw_entries') 
            ->select('user_id', DB::raw('SUM(entries_added) as total_entries'))
            ->groupBy('user_id')
            ->join('users', 'users.id', '=', 'prize_draw_entries.user_id') 
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(prize_draw_entries.entries_added) as total_entries') 
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_entries');
            
            if ($userId) {
                $query->where('users.id', $userId);
            }
            
            if ($limit > 0) {
                $query->limit($limit);
            }
            
            $result = $limit === 1 ? $query->first() : $query->get();
            
            if ($key && $result) {
                return $result->{$key} ?? null;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            return $limit === 1 ? null : collect();
        }
    }
}


if (!function_exists('current_user_entries')) {
    function current_user_entries()
    {
        if (!auth()->check()) {
            return 0;
        }
        $userId = auth()->id();
        try {
            $mainEntries = DB::table('prize_draw_entries')
            ->where('user_id', $userId)
            ->sum('entries_added');

            return $mainEntries;

            // $backupEntries = DB::table('prize_draw_entries_backups')
            //     ->where('user_id', $userId)
            //     ->sum('entries_added');
            // return $mainEntries + $backupEntries;

        } catch (\Exception $e) {
            return 0;
        }
    }
}



if (!function_exists('current_user_entries_percentage')) {
    function current_user_entries_percentage()
    {
        if (!auth()->check()) {
            return [
                'percentage' => 0,
                'current_entries' => 0,
                'top_entries' => 0
            ];
        }
        $currentEntries = current_user_entries();
        $topUser = user_total_entries();
        $topEntries = $topUser->total_entries ?? 1;
        return [
            'percentage' => $currentEntries > 0 
            ? round(($currentEntries / $topEntries) * 100, 2)
            : 0,
            'current_entries' => $currentEntries,
            'top_entries' => $topEntries
        ];
    }
}



if (!function_exists('generate_user_progress_circle')) {
    function generate_user_progress_circle()
    {
        // Get user entry data
        if (!auth()->check()) {
            $currentEntries = 0;
            $topEntries = 0;
            $percentage = 0;
        } else {
            $currentEntries = current_user_entries();
            $topUser = user_total_entries();
            $topEntries = $topUser->total_entries ?? 1;
            $percentage = ($currentEntries > 0) ? round(($currentEntries / $topEntries) * 100, 2) : 0;
        }

        // Calculate SVG properties
        // The circumference of a circle with r=60 is 2 * pi * 60 ≈ 377
        $dashoffset = 377 * (1 - min($percentage, 100) / 100);
        $roundedPercentage = round($percentage);
        $dashoffset=0;

        // Generate the HTML with interpolated values
        $html = "
        <div class='box-content d-flex align-items-center justify-content-center h-100'>
        <div class='progress-circle'>
        <svg width='150' height='150'>
        <circle class='progress-bg' cx='75' cy='75' r='60' />
        <circle class='progress-value' 
        cx='75' 
        cy='75' 
        r='60'
        stroke-dasharray='377'
        stroke-dashoffset='{$dashoffset}' />
        </svg>
        <div class='progress-text'>
        <a href='" . route('entries') . "' target='_blank'>
        <small>{$currentEntries}</small>
        </a>
        </div>
        </div>
        </div>
        ";

        return $html;
    }
}


if (!function_exists('is_featured_offers')) {
    function is_featured_offers($key = null)
    {
        $today = Carbon::today();
        $is_featured_offers = Offer::with(['organisation', 'categories'])
        ->where('is_featured', 'yes')
        // ->where('start_date', '<=', $today)
        // ->where('processing_status', OrganisationStatus::APPROVED)
        // ->where('end_date', '>=', $today)
        ->where('start_date', '<=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        })
        ->get();

        return $is_featured_offers;
    }
}


if (!function_exists('is_paid_advertisement_offers')) {
    function is_paid_advertisement_offers($key = null)
    {
        $today = Carbon::today();
        $is_paid_advertisement_offers = Offer::with(['organisation', 'categories'])
        ->where('is_paid_advertisement', 'yes')
        // ->where('start_date', '<=', $today)
        // ->where('processing_status', OrganisationStatus::APPROVED)
        // ->where('end_date', '>=', $today)
        ->where('start_date', '<=', $today)
        ->where('processing_status', OrganisationStatus::APPROVED)
        ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        })
        ->get();       
        return $is_paid_advertisement_offers;
    }
}



if (!function_exists('get_all_offers')) {
    function get_all_offers()
    {
        $today = Carbon::today();

        return Offer::with(['organisation', 'categories'])
            ->where('start_date', '<=', $today)
            ->where('processing_status', OrganisationStatus::APPROVED)
            ->where(function ($query) use ($today) {
                $query->where('end_date', '>=', $today)
                      ->orWhere('no_end_date', 1);
            })
            ->get();
    }
}



function generateStrongPassword($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=[]{}|;:,.<>/?';
    $password = '';
    $charLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $charLength - 1)];
    }
    return $password;
}



// if (!function_exists('upsell_modal_function')) {
//     function upsell_modal_function($key = null)
//     {
//         $user = auth()->user();
//         $currentDate = now();
//         $subscriptions = UserPackageSubscription::where('user_id', $user->id)
//         ->orderBy('starts_at', 'desc')
//         ->get();

//         $package_count = $subscriptions->count();
//         $package_plan = null;

//         if ($package_count === 0) {
//             $package_plan = Plan::find(8);
//         }else{

//             $currentSubscriptions = UserPackageSubscription::where('user_id', $user->id)
//             ->where('starts_at', '<=', $currentDate)
//             ->where('ends_at', '>=', $currentDate)
//             ->get();
//             $package_plan = null; 
//             $package_count = $currentSubscriptions->count();
//             if ($package_count === 1) {
//                 $firstPackage = $currentSubscriptions->first();
//                 if (($firstPackage->created_at ?? null)?->isToday()) {
//                     $plan = Plan::find($firstPackage->plan_id);
//                     if ($plan && $plan->type === 'package') {
//                         $package_plan = $plan;
//                     }
//                 }
//             }

//             $isupsellcurrentSubscriptions = UserPackageSubscription::where('user_id', $user->id)->where('is_upsell', 1)
//             ->where('starts_at', '<=', $currentDate)
//             ->where('ends_at', '>=', $currentDate)
//             ->get();

//             $package_count = $isupsellcurrentSubscriptions->count();
//             if ($package_count >= 1) {
//                $package_plan = null; 
//            }

//        }

//        return $package_plan;
//    }
// }



if (!function_exists('upsell_modal_function')) {
    function upsell_modal_function($key = null)
    {

        

        $user = auth()->user();
        $currentDate = now();

        // All user package subscriptions
        $subscriptions = UserPackageSubscription::where('user_id', $user->id)
            ->orderBy('starts_at', 'desc')
            ->get();

        // Total count (first time user check)
        $package_count = $subscriptions->count();
        $package_plan = null;

        // If user has NO past packages → return default package (first time)
        if ($package_count === 0) {
            $package_plan = Plan::find(8);

            return false;

            // return [
            //     'package_plan'  => $package_plan,
            //     'package_count' => $package_count, // 0
            // ];
        }

        // Get CURRENT active package subscriptions
        $currentSubscriptions = UserPackageSubscription::where('user_id', $user->id)
            ->where('starts_at', '<=', $currentDate)
            ->where('ends_at', '>=', $currentDate)
            ->get();

        $package_count = $currentSubscriptions->count();

        // If exactly 1 active package → check if created today + type=package
        if ($package_count === 1) {
            $firstPackage = $currentSubscriptions->first();

            if (($firstPackage->created_at ?? null)?->isToday()) {
                $plan = Plan::find($firstPackage->plan_id);
                if ($plan && $plan->type === 'package') {
                    $package_plan = $plan;
                }
            }
        }

        // Prevent upsell if the current active package is already an upsell
        $upsellActive = UserPackageSubscription::where('user_id', $user->id)
            ->where('is_upsell', 1)
            ->where('starts_at', '<=', $currentDate)
            ->where('ends_at', '>=', $currentDate)
            ->exists();

        if ($upsellActive) {
            $package_plan = null;
        }

        // Return both values
        return [
            'package_plan'  => $package_plan,
            'package_count' => $package_count, // active count
        ];
    }
}




//     if (!function_exists('upsell_modal_function')) {
//     function upsell_modal_function($key = null)
//     {
//         $user = auth()->user();
//         $currentDate = now();
//         $hasSubscriptions = UserPackageSubscription::where('user_id', $user->id)->exists();
//         if (!$hasSubscriptions) {
//             return Plan::find(8);
//         }
//         $hasUpsell = UserPackageSubscription::where('user_id', $user->id)
//             ->where('is_upsell', 1)
//             ->where('starts_at', '<=', $currentDate)
//             ->where('ends_at', '>=', $currentDate)
//             ->exists();

//         if ($hasUpsell) {
//             return null;
//         }
//         $currentSubscription = UserPackageSubscription::where('user_id', $user->id)
//             ->where('starts_at', '<=', $currentDate)
//             ->where('ends_at', '>=', $currentDate)
//             ->first();

//         if ($currentSubscription) {
//             $plan = Plan::find($currentSubscription->plan_id);
//             if ($currentSubscription->created_at->isToday()) {
//             if ($plan && $plan->type === 'package') {
//                 return $plan;
//             }
//         }
//         }
//         return null;
//     }
// }



if (!function_exists('user_active_subscription_package')) {
    function user_active_subscription_package()
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        return UserPackageSubscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->orderBy('starts_at', 'desc')
        ->first(); 
    }
}

if (!function_exists('getInstagramPosts')) {
    function getInstagramPosts($limit = 10)
    {
        $accessToken = env('INSTAGRAM_ACCESS_TOKEN');
        $userId = env('INSTAGRAM_USER_ID');

        $response = Http::get("https://graph.instagram.com/{$userId}/media", [
            'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
            'access_token' => $accessToken,
            'limit' => $limit
        ]);

        return $response->successful() ? $response->json()['data'] : [];
    }
}



if (!function_exists('award_first_purchase_bonus_entries')) {
    function award_first_purchase_bonus_entries(User $user, UserPackageSubscription $subscription): ?int
    {

       //  $userTotalPackageSubscriptions = UserPackageSubscription::where('user_id', $user->id)->count();
       //  if ($userTotalPackageSubscriptions === 1) {
       //      $numberOfFreeEntries = 4;
       //      $description = 'Free bonus entries for first package/subscription purchase';
       //      $now = Carbon::now();
       //      $entriesData = [];
       //      for ($i = 0; $i < $numberOfFreeEntries; $i++) {
       //       PrizeDrawEntry::create([
       //          'user_id'                  => $user->id,
       //          'package_subscriptions_id' => $subscription->id,
       //          'entries_added'            => 1,
       //          'description'              => $description,
       //          'type'                     => 'free',
       //      ]);
       //   }
       //     return $numberOfFreeEntries; 
       // }
       
       return 0;
   }
}




if (!function_exists('award_immediate_prize_entries')) {
    function award_immediate_prize_entries(User $user, Plan $plan, UserPackageSubscription $subscription): bool
    {
        $entriesCount = 0; 
        $description = '';
        $now = Carbon::now(); 
        if ($plan->type === 'package') {
         $entriesCount = $plan->access_duration_weeks; 
         $description = "Immediate package purchase award entryfor {$plan->name}";

     } elseif ($plan->type === 'subscription') {
        $entriesCount = 1; 
        $description = "Initial entry for new subscription: {$plan->name}";

    }
    if ($entriesCount > 0) {
        $entriesData = [];
        for ($i = 0; $i < $entriesCount; $i++) {
            PrizeDrawEntry::create([
                'user_id'                  => $user->id,
                'package_subscriptions_id' => $subscription->id,
                    'entries_added'            => 1, // Value is always 1
                    'description'              => $description,
                ]);
        }
    }
    return true;
}
}



if (!function_exists('activeSubscription')) {
    function activeSubscription(): bool
    {
       $user = $user ?? auth()->user();
       if (!$user) {
        return false; 
    }

    return UserPackageSubscription::where('user_id', $user->id)
    ->where('type', 'subscription')
    ->where('status', 'active')
    ->exists();
}
}


if (!function_exists('is_allowed_user')) {
    function is_allowed_user()
    {
        $allowedUserIds = [
            '34deb212-0c9a-4b06-b73d-85cb6171ccb5',
            '8f4e874b-6809-4b96-aa62-24ee5203ed4d',
            'ba73ba3d-504c-4d7b-80e2-ce95abd81184',
            'd481ddc0-3609-46e0-8562-91535f660aa9',
            'db2891e5-23d9-4240-aae0-2c552e33eda1',
            '0beeba52-c0eb-4420-80ba-434558212f2e',
            'c2a46676-12fb-4541-a5a8-7c352a3d3694',
        ];
        return $allowedUserIds;
    }
}



if (!function_exists('logUserActivity')) {
    function logUserActivity($userId, $event, $message = null, $meta = [], $type = null, $typeid = null)
    {
        \App\Models\UserActivityLog::create([
            'user_id' => $userId,
            'event'   => $event,
            'message' => $message,  // custom message
            'meta'    => $meta,
            'type'    => $type,
            'typeid'  => $typeid,
        ]);
    }
}



if (!function_exists('activePrizeDraws')) {
    function activePrizeDraws()
    {

      $prizeDraw = PrizeDraw::where('draw_date', '>=', now())
      ->where('winner_user_finalised', 'no')
      ->where('show_on_site', true)
      ->whereNull('winner_id')
      ->orderBy('draw_date', 'asc')
      ->orderBy('created_at', 'desc')
      ->get();

      $prizeDraw = PrizeDraw::withTrashed()
      ->where('draw_date', '>=', now())
      ->orderBy('draw_date', 'asc')
      ->orderBy('created_at', 'desc')
      ->get();
      return $prizeDraw;
  }
}





if (!function_exists('getLatestDrawCountdown')) {
    function getLatestDrawCountdown()
    {

      $nowBrisbane = Carbon::now('Australia/Brisbane');
        
       return $latestDraw = PrizeDraw::where('draw_date', '>=', $nowBrisbane)
            ->whereNull('winner_id')
            ->where('draw_type', 'packages')
            ->orderBy('draw_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->first();
        
  }
}


if (!function_exists('getSecondDrawCountdown')) {
    function getSecondDrawCountdown()
    {
        $nowBrisbane = Carbon::now('Australia/Brisbane');
        return PrizeDraw::where('draw_date', '>=', $nowBrisbane)
            ->whereNull('winner_id')
            ->where('draw_type', 'subscribers')
            ->orderBy('draw_date', 'asc')
            ->orderBy('created_at', 'desc')
            //->skip(1)   // ⬅️ first ko skip
            ->first();  // ⬅️ second draw
    }
}


if (!function_exists('upcoming_draws_carousel')) {
    function upcoming_draws_carousel()
    {

        return PrizeDraw::where('show_on_site', true)
        ->where('draw_date', '>', now())
        ->where('winner_user_finalised', 'no')
        ->where('draw_type', 'subscribers')
        ->orderBy('draw_date')
        ->get();
    }
}


if (!function_exists('get_google_reviews')) {
    function get_google_reviews() {
        return GoogleReview::latest()->get();
    }
}

if (!function_exists('getLiveCountsByPlanIds')) {
    function getLiveCountsByPlanIds(array $planIds)
    {
        return \App\Models\UserPackageSubscription::query()
            ->where('status', 'active')
            ->whereIn('plan_id', $planIds)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->selectRaw('plan_id, COUNT(*) as total')
            ->groupBy('plan_id')
            ->pluck('total', 'plan_id');
    }
}

if (!function_exists('getSalesOverview')) {


function getSalesOverview()
{
    $base = DB::table('user_package_subscriptions as ups')
        ->join('plans as p', 'ups.plan_id', '=', 'p.id')
        ->whereIn('ups.status', ['active', 'Paid']);

    $revenueExpression = "
        SUM(
            CASE 
                WHEN ups.is_upsell = 1 
                    THEN p.price / 2
                ELSE p.price
            END
        ) as total_revenue
    ";

    // Overall
    $overall = (clone $base)
        ->selectRaw("COUNT(ups.id) as total_sales, $revenueExpression")
        ->first();

    // This Month
    $thisMonth = (clone $base)
        ->whereMonth('ups.purchase_date', now()->month)
        ->whereYear('ups.purchase_date', now()->year)
        ->selectRaw("COUNT(ups.id) as total_sales, $revenueExpression")
        ->first();

    // Last Month
    $lastMonth = (clone $base)
        ->whereMonth('ups.purchase_date', now()->subMonth()->month)
        ->whereYear('ups.purchase_date', now()->subMonth()->year)
        ->selectRaw("COUNT(ups.id) as total_sales, $revenueExpression")
        ->first();

    // This Week
    $thisWeek = (clone $base)
        ->whereBetween('ups.purchase_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])
        ->selectRaw("COUNT(ups.id) as total_sales, $revenueExpression")
        ->first();

    return [
        'overall'   => $overall,
        'thisMonth' => $thisMonth,
        'lastMonth' => $lastMonth,
        'thisWeek'  => $thisWeek,
    ];
}

}



if (!function_exists('getStripeChargeRevenue')) {
    function getStripeChargeRevenue($start = null, $end = null): array
    {
        Stripe::setApiKey(config('cashier.secret'));

        $total = 0;
        $startingAfter = null;
        $hasMore = true;

        while ($hasMore) {
            $params = ['limit' => 100];

            if ($start && $end) {
                $params['created'] = [
                    'gte' => $start->timestamp,
                    'lte' => $end->timestamp,
                ];
            }

            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $charges = Charge::all($params);

            foreach ($charges->data as $charge) {
                if (!$charge->paid) continue;
                $total += ($charge->amount - $charge->amount_refunded) / 100;
            }

            $hasMore = $charges->has_more;
            if ($hasMore) {
                $startingAfter = end($charges->data)->id;
            }
        }

        return [
            'total'      => round($total, 2),
            'start_date' => $start?->toDateString(),
            'end_date'   => $end?->toDateString(),
        ];
    }
}

if (!function_exists('getStripeSalesData')) {
    function getStripeSalesData(): array
    {
        return cache()->remember('stripe_sales_data', now()->addHours(6), function () {
            return [
                'overall'   => getStripeChargeRevenue(),
                'thisMonth' => getStripeChargeRevenue(now()->startOfMonth(), now()->endOfMonth()),
                'lastMonth' => getStripeChargeRevenue(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()),
                'thisWeek'  => getStripeChargeRevenue(now()->startOfWeek(), now()->endOfWeek()),
            ];
        });
    }
}

