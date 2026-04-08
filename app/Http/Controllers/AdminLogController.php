<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminLog;
use App\Models\UserLog;
use App\Models\Admin; 
use App\Models\User; 
use Illuminate\Support\Facades\DB;

use Carbon\Carbon; // For date manipulation

class AdminLogController extends Controller
{
    public function showLog(Request $request, $type,$id)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $logDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        if ($type=='admins') {
           $admin = Admin::findOrFail($id); 
           $logs = AdminLog::where('admin_id', $admin->id)->whereDate('created_at', $logDate)->orderBy('created_at', 'asc')->get();
           $back_route=route('admins.index');
       }elseif ($type=='users') {
           $admin = User::findOrFail($id); 
           $logs = UserLog::where('user_id', $admin->id)->whereDate('created_at', $logDate)->orderBy('created_at', 'asc')->get();
           $back_route=route('users.index');
       }else{
            return redirect()->back()->with('error', 'Invalid log type specified. Please use "admins" or "users".');
       }
       

        $tableRows = [];
        $adminModules = [];
        $minTstamp = null;
        $maxTstamp = null;
        $diffMinutes = 0;
        $start_date = null;
        $processedLogs = []; 

        if ($logs->isNotEmpty()) {
            foreach ($logs as $i => $log) {
                if ($minTstamp === null || $log->created_at->lt($minTstamp)) {
                    $minTstamp = $log->created_at; 
                }
                if ($maxTstamp === null || $log->created_at->gt($maxTstamp)) {
                    $maxTstamp = $log->created_at;
                }
                if ($start_date === null) {
                    $start_date = $log->created_at;
                }
                
                $now = $log->created_at;
                $intervalSeconds = abs($now->timestamp - $start_date->timestamp);
                $diffMinutes = round($intervalSeconds / 60);
                $recordOutput = '';

                $tableRows[$i] = '<tr>';
                $tableRows[$i] .= '<td>' . $log->created_at->format("g:ia, D j/n/Y") . '</td>';
                $tableRows[$i] .= '<td>' . $admin->name . '</td>'; 
                $tableRows[$i] .= '<td>' . $log->module . '</td>';

                if ($log->module == 'ping') {
                    $tableRows[$i] .= '<td>' . $log->module_action . '</td>';
                    $tableRows[$i] .= '<td>' . $log->url . '</td>';
                } else {
                    $tableRows[$i] .= '<td>' . $log->module_action . '</td>';
                    $tableRows[$i] .= '<td>' . $recordOutput . '</td>';
                }
                $tableRows[$i] .= '</tr>';

                $adminModules[$log->module] = true;

                $logOutput = $log->created_at->format("g:ia, D j/n/Y") . ' -- ' . $log->module . ' -- ';
                if ($log->module == 'ping') {
                    $logOutput .= $log->url;
                } else {
                    $logOutput .= $log->module_action;
                    if ($recordOutput != '') {
                        $logOutput .= ' :: ' . $recordOutput;
                    }
                }

                if (!isset($processedLogs[$diffMinutes])) {
                    $processedLogs[$diffMinutes] = [];
                }
                if (!isset($processedLogs[$diffMinutes][$log->module])) {
                    $processedLogs[$diffMinutes][$log->module] = [];
                }
                $processedLogs[$diffMinutes][$log->module][] = [
                    "module_action" => $log->module_action,
                    "tstamp" => $log->created_at->toDateTimeString(), 
                    "output" => $logOutput
                ];
            }
        }

        $totalDiffMinutes = $diffMinutes; 
        $totalDeadtime = 0;
        $deadtimeLeeway = 10;
        $visualTableData = []; 

        $outputStatuses = []; 
        for ($minute = 0; $minute <= $totalDiffMinutes; $minute++) {
            $outputStatuses[$minute]['type'] = isset($processedLogs[$minute]) ? 'success' : 'warning';
        }

        $colspan = 0;
        $outputted = false;
        $deadTimeCells = [];

        for ($minute = 0; $minute <= $totalDiffMinutes; $minute++) {
            $currentType = $outputStatuses[$minute]['type'];
            $previousType = ($minute != 0) ? $outputStatuses[$minute - 1]['type'] : null;

            if ($minute == 0 || $currentType == $previousType) {
                $colspan++;
                $outputted = false;
            } else {
                if ($colspan > $deadtimeLeeway && $previousType == 'warning') {
                    $timeOutput = $this->formatDuration($colspan);
                    $deadTimeCells[] = '<td class="danger" colspan="' . $colspan . '">' . $timeOutput . '</td>';
                    $totalDeadtime += $colspan;
                } else {
                    $deadTimeCells[] = '<td class="' . $previousType . '" colspan="' . $colspan . '">&nbsp;</td>';
                }
                $colspan = 1;
                $outputted = true;
            }
        }
        if ($outputted == false) {
           if ($colspan > $deadtimeLeeway && $outputStatuses[$totalDiffMinutes]['type'] == 'warning') {
            $timeOutput = $this->formatDuration($colspan);
            $deadTimeCells[] = '<td class="danger" colspan="' . $colspan . '">' . $timeOutput . '</td>';
            $totalDeadtime += $colspan;
        } else {
            $deadTimeCells[] = '<td class="' . $outputStatuses[$totalDiffMinutes]['type'] . '" colspan="' . $colspan . '">&nbsp;</td>';
        }
    }


    $totalDeadtimeHours = floor($totalDeadtime / 60);
    $totalDeadtimeMinutes = $totalDeadtime % 60;
    $actionDurationOutput = '0 hour 0 minutes';
    $totalActionOutput = '0 hour 0 minutes';

    if ($minTstamp && $maxTstamp) {
        $durationInterval = $maxTstamp->diff($minTstamp);
        $actionDurationOutput = $durationInterval->h . " hour" . $this->pluralise($durationInterval->h) . " " . $durationInterval->i . " minutes";

        $intervalMinutes = abs($minTstamp->timestamp - $maxTstamp->timestamp) / 60;
        $totalAction = $intervalMinutes - $totalDeadtime;
        $totalActionOutput = $this->formatDuration($totalAction);
    }

    return view('admin.logs.show', compact(
        'type',
        'back_route',
        'admin',
        'logDate',
        'logs', 
        'tableRows', 
        'adminModules', 
        'start_date', 
        'totalDiffMinutes',
        'processedLogs', 
        'totalDeadtime',
        'deadtimeLeeway',
        'totalDeadtimeHours',
        'totalDeadtimeMinutes',
        'actionDurationOutput',
        'totalActionOutput',
        'minTstamp',
        'maxTstamp',
        'deadTimeCells'
    ));
}


public function showUserDailyActions($id)
{
    $admin = Admin::findOrFail($id); 
    $logData = DB::table('admin_logs')
    ->select(
        DB::raw('MIN(created_at) as first_action'),
        DB::raw('MAX(created_at) as last_action')
    )
    ->where('admin_id', $id)
    ->where('created_at', '>=', Carbon::now()->subDays(30))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->orderBy('first_action', 'DESC')
    ->get();

    $processedLogData = [];
    foreach ($logData as $log) {
        $firstAction = Carbon::parse($log->first_action);
        $lastAction = Carbon::parse($log->last_action);
        $interval = $firstAction->diff($lastAction);
        $duration = '';
        if ($interval->h > 0) {
            $duration .= $interval->h . ' Hours ';
        }
        if ($interval->i > 0) {
            $duration .= $interval->i . ' Minutes';
        }
        if (empty($duration)) {
            $duration = 'Less than a minute';
        }

        $processedLogData[] = [
            'day' => $firstAction->format('D j/n/Y'),
            'first_action_time' => $firstAction->format('g:ia'),
            'last_action_time' => $lastAction->format('g:ia'),
            'duration' => trim($duration),
        ];
    }

    return view('admin.logs.user_daily_actions', [
        'admin' => $admin,
        'logData' => $processedLogData,
        'adminId' => $id,
    ]);
}

    // Helper function for pluralization (can be global helper or trait)
private function pluralise($count)
{
    return ($count == 1) ? '' : 's';
}

    // Helper function to format duration string
private function formatDuration($minutes)
{
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;
    return $hours . ' hour' . $this->pluralise($hours) . ' and ' . $remainingMinutes . ' minute' . $this->pluralise($remainingMinutes);
}
}