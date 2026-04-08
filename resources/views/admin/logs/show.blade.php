@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div id="admin_wrap">
                    <div class="card-header fw-bold">{{ $admin->name }}</div>
                    <div class="card-body">
                        <div class="mb-3 text-end">
                            <a href="{{ $back_route }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to Admin Users Admin
                            </a>
                        </div>
                        
                        <div class="row align-items-center mb-4">
                            <div class="col-md-4 text-start">
                                <a class="btn btn-default" href="{{ route('admin.logs.show', ['type' => $type,'id' => $admin->id, 'date' => $logDate->copy()->subDay()->format('Y-m-d')]) }}">
                                    Previous Day
                                </a>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <label for="pick_date" class="control-label mb-0 me-2">Pick a Date:</label>
                                </div>
                                <div>
                                    <form class="d-flex align-items-center" action="{{ route('admin.logs.show', ['type' => $type,'id' => $admin->id]) }}" method="GET">
                                        <input  name="date" type="text" class="form-control me-2 datepicker" value="{{ $logDate->format('Y-m-d') }}" placeholder="{{ $logDate->format('Y-m-d') }}">
                                        <input class=" btn btn-default btn-sm" type="submit" value="Go">
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @php
                                $tomorrow = $logDate->copy()->addDay(); 
                                $now = now(); 
                                @endphp

                                @if ($tomorrow->isBefore($now))
                                <a class="btn btn-default" href="{{ route('admin.logs.show', ['type' => $type,'id' => $admin->id, 'date' => $logDate->copy()->addDay()->format('Y-m-d')]) }}">
                                    Next Day
                                </a>
                                @else
                                <div style="min-width: 100px;"></div>
                                @endif
                            </div>
                        </div>
                        <hr>
                    </div>

                    @if ($logs->isNotEmpty())
                    <div class="card-body">
                        <div class="box clearfix">
                            <h2>Visual log for {{ $admin->name }}, {{ $logDate->format('D j/n/y') }}</h2>
                            <div class="table-responsive">
                                <table id="visual_log" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="sticky-col">Module</th>
                                            @php
                                            $interval = 30; 
                                            @endphp
                                            @for ($minute = 0; $minute <= $totalDiffMinutes; $minute += $interval)
                                            @if ($minute == 0)
                                            @php
                                            $startMinute = $start_date ? $start_date->minute : 0;
                                            $firstIntervalColspan = ($startMinute >= 30) ? (60 - $startMinute) : (30 - $startMinute);
                                            if($firstIntervalColspan == 0) $firstIntervalColspan = 30;
                                            if($firstIntervalColspan > ($totalDiffMinutes + 1)) $firstIntervalColspan = $totalDiffMinutes + 1;
                                            @endphp
                                            @if ($start_date)
                                            <th colspan="{{ $firstIntervalColspan }}">{{ $start_date->format("g:ia") }}</th>
                                            @else
                                            <th colspan="{{ $firstIntervalColspan }}">--:--</th>
                                            @endif
                                            @php $minute += $firstIntervalColspan - $interval; @endphp
                                            @else
                                            @php
                                            $currentIntervalTime = $start_date ? $start_date->copy()->addMinutes($minute) : null;
                                            $currentIntervalColspan = 30;
                                            if (($minute + $currentIntervalColspan) > ($totalDiffMinutes + 1)) {
                                                $currentIntervalColspan = ($totalDiffMinutes + 1) - $minute;
                                            }
                                            if($currentIntervalColspan <= 0) continue;
                                            @endphp
                                            @if($currentIntervalTime)
                                            <th colspan="{{ $currentIntervalColspan }}">{{ $currentIntervalTime->format("g:ia") }}</th>
                                            @else
                                            <th colspan="{{ $currentIntervalColspan }}">--:--</th>
                                            @endif
                                            @endif
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adminModules as $adminModule => $data)
                                        <tr>
                                            <th nowrap class="sticky-col">{{ $adminModule }}</th>
                                            @for ($minute = 0; $minute <= $totalDiffMinutes; $minute++)
                                            <td width="10" class="@if(isset($processedLogs[$minute][$adminModule])) success @endif"
                                            @if(isset($processedLogs[$minute][$adminModule]))
                                            data-hover="<ul>@foreach($processedLogs[$minute][$adminModule] as $logData)<li>{{ htmlspecialchars($logData['output'], ENT_QUOTES) }}</li>@endforeach</ul>"
                                            @endif
                                            ></td>
                                            @endfor
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th nowrap class="sticky-col">Dead time</th>
                                            {!! implode('', $deadTimeCells) !!}
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="clearfix mt-2">
                                <hr>
                                <div class="clearfix mt-2">
                                    <span class="alert alert-info">
                                        <strong>Action Duration: {{ $actionDurationOutput }}</strong>
                                        @if($minTstamp)
                                        -- First action: {{ $minTstamp->format("g:ia") }}
                                        @endif
                                        @if($maxTstamp)
                                        -- Last action: {{ $maxTstamp->format("g:ia") }}
                                        @endif
                                    </span>
                                </div>
                                <hr>
                                <div class="clearfix">
                                    <span class="alert alert-danger">
                                        <strong>Total Dead Time: {{ $totalDeadtimeHours }} hour{{ $totalDeadtimeHours == 1 ? '' : 's' }} and {{ $totalDeadtimeMinutes }} minutes</strong>
                                        - the user must be inactive for {{ $deadtimeLeeway }} consecutive minutes to record Dead Time.
                                    </span>
                                </div>
                                <hr>
                                <div class="clearfix">
                                    <span class="alert alert-success">
                                        <strong>Total Active Time: {{ $totalActionOutput }}</strong> - Action Duration minus Total Dead Time.
                                    </span>
                                </div>
                            </div>


                        </div>
                    </div>

                    @endif



                    <div class="card-body">
                        <div class="box clearfix">
                            <h2>User log for {{ $admin->name }}, {{ $logDate->format('D j/n/y') }}</h2>
                            @if ($logs->isNotEmpty())
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr><th>Time</th><th>User</th><th>Module</th><th>Action</th><th>Record</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (array_reverse($tableRows) as $row)
                                    {!! $row !!}
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="alert alert-danger">No activity logged!</div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
   {{--   if (typeof jQuery !== 'undefined') { 
        jQuery('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd', 
            autoclose: true 
        });
    } --}}
    var popoverTriggerList = [].slice.call(document.querySelectorAll('td[data-hover]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        var genericCloseBtnHtml = '<button type="button" class="btn-close" aria-label="Close" onclick="this.closest(\'div.popover\').remove();"></button>';

        return new bootstrap.Popover(popoverTriggerEl, {
            placement: "right",
            trigger: "hover focus", 
            container: "#admin_wrap", 
            html: true,
            title: "Action Log" + genericCloseBtnHtml,
            content: popoverTriggerEl.getAttribute('data-hover'),
            customClass: "my-custom-popover-width" 

        });
    });
});
</script>

@endsection