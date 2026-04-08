<?php
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use App\Models\Organisation;
use App\Models\Admin;
use App\Models\User;
use App\Models\AdminLog;
use Carbon\Carbon; // For current date



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

if (!function_exists('get_total_offer_count')) {
    function get_total_offer_count(): int
    {
        return Offer::count();
    }
}

if (!function_exists('get_total_user_count')) {
    function get_total_user_count(): int
    {
        return User::count();
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
            // 'address_lat'               => 'Lat (Optional)',
            // 'address_lng'               => 'Lng (Optional)',
            // 'address_street_number'     => 'Street Number (Optional)',
            // 'address_street_name'       => 'Street Name (Optional)',
            // 'address_city'              => 'City (Optional)',
            // 'address_state'             => 'State (Optional)',
            // 'address_postal_code'       => 'Postal Code (Optional)',
            // 'address_country'           => 'Country (Optional)',
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
            '3' => 'CHARITY'
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



if (!function_exists('is_featured_offers')) {
    function is_featured_offers($key = null)
    {
       $is_featured_offers = Offer::with(['organisation', 'categories'])
            ->where('is_featured', 'yes')
            ->where('end_date', '>=', Carbon::now()) 
            ->get();
       
        return $is_featured_offers;
    }
}
