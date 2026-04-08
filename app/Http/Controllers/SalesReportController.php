<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Exports\SalesReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    // Stripe invoices = 'paid', Stripe charges = 'succeeded'
    private array $paidStatuses = ['paid', 'succeeded'];

    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        $summary       = $this->getSummary($startDate, $endDate);
        $monthly       = $this->getMonthly($startDate, $endDate);
        $perUser       = $this->getPerUser($startDate, $endDate);
        $byState       = $this->getByState($startDate, $endDate);
        $subscriptions = $this->getSubscriptionDetail($startDate, $endDate);
        $oneTimeList   = $this->getOneTimeDetail($startDate, $endDate);

        return view('admin.reports-sales', compact(
            'summary',
            'monthly',
            'perUser',
            'byState',
            'subscriptions',
            'oneTimeList',
            'startDate',
            'endDate'
        ));
    }

    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        return Excel::download(
            new SalesReportExport($startDate, $endDate),
            'sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        $summary       = $this->getSummary($startDate, $endDate);
        $monthly       = $this->getMonthly($startDate, $endDate);
        $perUser       = $this->getPerUser($startDate, $endDate);
        $byState       = $this->getByState($startDate, $endDate);
        $subscriptions = $this->getSubscriptionDetail($startDate, $endDate);
        $oneTimeList   = $this->getOneTimeDetail($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.sales-pdf', compact(
            'summary',
            'monthly',
            'perUser',
            'byState',
            'subscriptions',
            'oneTimeList',
            'startDate',
            'endDate'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('sales-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    // ─── PRIVATE HELPERS ───────────────────────────────────────────────────────

    private function getDateRange(Request $request): array
    {
        $filter = $request->get('filter', 'this_month');

        return match ($filter) {
            'today'      => [Carbon::today(), Carbon::today()->endOfDay()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'yearly'     => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom'     => [
                Carbon::parse($request->get('from', now()->startOfMonth())),
                Carbon::parse($request->get('to', now()->endOfMonth()))->endOfDay(),
            ],
            default      => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function getSummary(Carbon $start, Carbon $end): array
    {
        $base = Transaction::whereIn('status', $this->paidStatuses)
            ->whereBetween('paid_at', [$start, $end]);

        $total      = (clone $base)->sum('amount');
        $totalFee   = (clone $base)->sum('stripe_fee');
        $totalNet   = (clone $base)->sum('net_amount');
        $totalCount = (clone $base)->count();

        $subRevenue = (clone $base)->where('type', 'subscription')->sum('amount');
        $subCount   = (clone $base)->where('type', 'subscription')->count();
        $otRevenue  = (clone $base)->where('type', 'one_time')->sum('amount');
        $otCount    = (clone $base)->where('type', 'one_time')->count();

        return [
            'total_revenue'           => $total,
            'total_stripe_fee'        => $totalFee,
            'total_net'               => $totalNet,
            'total_transactions'      => $totalCount,

            'subscription_revenue'    => $subRevenue,
            'subscription_count'      => $subCount,
            'subscription_percentage' => $total > 0 ? round(($subRevenue / $total) * 100, 1) : 0,

            'one_time_revenue'        => $otRevenue,
            'one_time_count'          => $otCount,
            'one_time_percentage'     => $total > 0 ? round(($otRevenue / $total) * 100, 1) : 0,
        ];
    }

    private function getMonthly(Carbon $start, Carbon $end)
    {
        return Transaction::whereIn('status', $this->paidStatuses)
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("
                DATE_FORMAT(paid_at, '%M %Y') as period,
                DATE_FORMAT(paid_at, '%Y-%m') as sort_key,
                SUM(amount) as total_revenue,
                SUM(stripe_fee) as total_fee,
                SUM(net_amount) as total_net,
                COUNT(*) as total_transactions,
                SUM(CASE WHEN type = 'subscription' THEN amount ELSE 0 END) as subscription_revenue,
                SUM(CASE WHEN type = 'one_time' THEN amount ELSE 0 END) as one_time_revenue,
                COUNT(CASE WHEN type = 'subscription' THEN 1 END) as subscription_count,
                COUNT(CASE WHEN type = 'one_time' THEN 1 END) as one_time_count
            ")
            ->groupByRaw("DATE_FORMAT(paid_at, '%Y-%m'), DATE_FORMAT(paid_at, '%M %Y')")
            ->orderByRaw("MIN(paid_at) DESC")
            ->get();
    }

    private function getPerUser(Carbon $start, Carbon $end)
    {
        return Transaction::whereIn('transactions.status', $this->paidStatuses)
            ->whereBetween('transactions.paid_at', [$start, $end])
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->selectRaw("
                COALESCE(users.name, transactions.billing_name, 'Guest') as customer_name,
                COALESCE(users.email, transactions.billing_email, 'N/A') as customer_email,
                SUM(transactions.amount) as total_revenue,
                SUM(transactions.stripe_fee) as total_fee,
                SUM(transactions.net_amount) as total_net,
                COUNT(*) as total_transactions,
                SUM(CASE WHEN transactions.type = 'subscription' THEN transactions.amount ELSE 0 END) as subscription_revenue,
                SUM(CASE WHEN transactions.type = 'one_time' THEN transactions.amount ELSE 0 END) as one_time_revenue
            ")
            ->groupBy('transactions.user_id', 'users.name', 'users.email', 'transactions.billing_name', 'transactions.billing_email')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getByState(Carbon $start, Carbon $end)
    {
        $totalRevenue = Transaction::whereIn('status', $this->paidStatuses)
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        return Transaction::whereIn('status', $this->paidStatuses)
            ->whereBetween('paid_at', [$start, $end])
            ->whereNotNull('billing_state')
            ->selectRaw("
                billing_state,
                billing_country,
                SUM(amount) as total_revenue,
                COUNT(*) as total_transactions,
                ROUND((SUM(amount) / ?) * 100, 1) as percentage
            ", [$totalRevenue ?: 1])
            ->groupBy('billing_state', 'billing_country')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getSubscriptionDetail(Carbon $start, Carbon $end)
    {
        return Transaction::whereIn('status', $this->paidStatuses)
            ->where('type', 'subscription')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("
                COALESCE(product_name, description, 'Subscription') as plan_name,
                price_id,
                COUNT(*) as total_transactions,
                SUM(amount) as total_revenue,
                SUM(stripe_fee) as total_fee,
                SUM(net_amount) as total_net
            ")
            ->groupBy('product_name', 'description', 'price_id')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getOneTimeDetail(Carbon $start, Carbon $end)
    {
        return Transaction::whereIn('status', $this->paidStatuses)
            ->where('type', 'one_time')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("
                COALESCE(product_name, description, 'One-Time Payment') as product_name,
                price_id,
                COUNT(*) as total_transactions,
                SUM(amount) as total_revenue,
                SUM(stripe_fee) as total_fee,
                SUM(net_amount) as total_net
            ")
            ->groupBy('product_name', 'description', 'price_id')
            ->orderByDesc('total_revenue')
            ->get();
    }
}