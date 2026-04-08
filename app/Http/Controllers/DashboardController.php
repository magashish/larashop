<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\UserPackageSubscription;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with all statistics
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get package statistics (total and by tier)
        $packageStats = $this->getPackageStats();
        // Get page engagement statistics
       // $pageEngagements = $this->getPageEngagementStats();
        $pageEngagements = '';
        
        // Return the dashboard view with all data
        return view('admin.stats-chart-dashboard', compact('packageStats', 'pageEngagements'));
    }

    /**
     * Get data path statistics for line graph
     * Returns daily counts for the specified number of days
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataPathStats(Request $request)
    {
        // Get the number of days from request, default to 30 days
        $days = $request->get('days', 30);
        
        // Query the database for daily counts
        // Group by date and count records per day
        $dataPath = UserPackageSubscription::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('type', 'package')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Return as JSON for Chart.js
        return response()->json($dataPath);
    }

    /**
     * Get package purchase statistics
     * Returns total packages and breakdown by tier (Bronze, Silver, Gold)
     * 
     * @return array
     */
    public function getPackageStats()
    {
        // Define package IDs for each tier
        $packageTiers = [
            8 => 'gold',
            20 => 'silver',
            21 => 'bronze',
        ];

        // Get total packages purchased (all three tiers)
        $total = UserPackageSubscription::whereIn('plan_id', [8, 20, 21])->count();

    // Get count by each tier
        $byTier = [
            'bronze' => UserPackageSubscription::where('plan_id', 8)->count(),
            'silver' => UserPackageSubscription::where('plan_id', 20)->count(),
            'gold' => UserPackageSubscription::where('plan_id', 21)->count(),
        ];
   
        
        // Return statistics array
        return [
            'total' => $total,
            'by_tier' => $byTier,
        ];
    }

    /**
     * Get page engagement statistics for business pages
     * Returns unique user counts who engaged with each page
     * 
     * @return array
     */
    public function getPageEngagementStats()
    {
        // Count unique users who engaged with Mazda business page
        $mazdaEngagements = PageEngagement::where('page_name', 'mazda')
            ->distinct('user_id')
            ->count('user_id');
        
        // Count unique users who engaged with Grange Social business page
        $grangeSocialEngagements = PageEngagement::where('page_name', 'grange_social')
            ->distinct('user_id')
            ->count('user_id');
        
        // Return engagement counts for both pages
        return [
            'mazda' => $mazdaEngagements,
            'grange_social' => $grangeSocialEngagements,
        ];
    }
}