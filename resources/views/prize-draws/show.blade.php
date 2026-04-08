@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold ">{{ __('Prize Draws') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('prize_draws.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Prize Draws Admin
                        </a>
                    </div>

                    <div class="mb-3">
                        @if($prizeDraw->winner_id || $prizeDraw->winner_user_finalised=='yes')
                        <div class="alert alert-dark border-warning">
                            <h5 class="alert-heading "><i class="bi bi-check-circle-fill"></i> Winner Finalized!</h5>
                            <p class="mb-0">
                                <strong>Winner:</strong>
                                @if($prizeDraw->winner)
                                {{ $prizeDraw->winner->name
                                    ? $prizeDraw->winner->name
                                    : ($prizeDraw->winner->first_name
                                        ? $prizeDraw->winner->first_name . ' ' . $prizeDraw->winner->last_name
                                        : $prizeDraw->winner->email)
                                    }}


                                    (Email: {{ $prizeDraw->winner->email }})
                                    (Phone Number: {{ $prizeDraw->winner->mobile }})
                                    @else
                                    User ID: {{ $prizeDraw->winner_id }} (User Not Found)
                                    @endif
                                    <br>
                                    <strong>Draw Date/Time:</strong> {{ $prizeDraw->draw_datetime ? $prizeDraw->draw_datetime->format('Y-m-d H:i:s') : 'N/A' }}
                                </p>
                            </div>
                            @endif
                        </div>

                        @if($prizeDraw->winner_id && $prizeDraw->winner_user_finalised=='yes')
                        <div class="text-center">
                            @php
                            if ($prizeDraw->winner) {
                                // $winnerFirst = trim($prizeDraw->winner->first_name ?? '');
                                // $winnerLastInitial = !empty($prizeDraw->winner->last_name)
                                // ? strtoupper(substr($prizeDraw->winner->last_name, 0, 1)) . '.'
                                // : '';

                                // if (empty($winnerFirst) && empty($winnerLastInitial)) {
                                //     $winner_name_formatted = $prizeDraw->winner->email ?? 'No winner info available';
                                // } else {
                                //     $winner_name_formatted = trim($winnerFirst . ' ' . $winnerLastInitial);
                                // }

                                $winnerName         = trim($prizeDraw->winner->name ?? '');
                                $winnerFirst        = trim($prizeDraw->winner->first_name ?? '');
                                $winnerLastInitial  = !empty($prizeDraw->winner->last_name)
                                ? strtoupper(substr($prizeDraw->winner->last_name, 0, 1)) . '.'
                                : '';

                                if (!empty($winnerName)) {
                                    $fullName = $winnerName;
                                    $nameParts = preg_split('/\s+/', trim($fullName));
                                    $firstName = $nameParts[0] ?? '';
                                   // $lastName  = $nameParts[1] ?? '';
                                    $lastName = end($nameParts);
                                    $lastInitial = strtoupper(substr($lastName, 0, 1));
                                    $winner_name_formatted = $firstName . ' ' . substr($lastName, 0, 1) . '.';

                                } elseif (!empty($winnerFirst)) {
                                    $winner_name_formatted = trim($winnerFirst . ' ' . $winnerLastInitial);
                                } else {
                                    $winner_name_formatted = $prizeDraw->winner->email;
                                }


                            }
                            @endphp

                            @if(!empty($winner_name_formatted) && $winner_name_formatted !== 'No winner info available')
                            <button 
                            id="revealButton"
                            class="btn btn-lg reveal-btn"
                            data-name="{{ $winner_name_formatted }}"
                            data-bs-toggle="modal"
                            data-bs-target="#winnerRevealModal">
                            <i class="bi bi-trophy-fill"></i> Reveal Winner
                        </button>

                        <button  style="display: none;" 
                        id="newrevealButton"
                        class="btn btn-lg reveal-btn"
                        data-name="{{ $winner_name_formatted }}"
                        data-bs-toggle="modal"
                        data-bs-target="#newwinnerRevealModal">
                        <i class="bi bi-trophy-fill"></i> Reveal Winner New
                    </button>
                    @endif


                </div>
                @else
                <form action="{{ route('prize_draws.update', $prizeDraw->id) }}" method="POST">
                    @csrf
                    @method('PUT') 
                    <div class="mb-3 text-end">
                        <p>Click the button below to draw a winner for this prize draw. This action will finalize the winner and move all associated entries to a backup table.</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        @if($prizeDraw->winner_id || $prizeDraw->winner_user_finalised=='yes')
                        <button type="submit" name="action" value="redraw" class="btn btn-warning">
                            <i class="bi bi-arrow-clockwise"></i> ReDraw Winner
                        </button>

                        <button
                        type="submit"
                        name="action"
                        value="finalize"
                        class="btn"
                        onclick="return confirm('Are you sure you want to finalize the winner? This action cannot be undone.');">
                        <i class="bi bi-check-circle"></i> Finalize Winner
                    </button>


                    @else
                    <button type="submit" name="action" value="draw" class="btn btn-warning">
                        <i class="bi bi-gift"></i> Draw Winner Now
                    </button>
                    @endif

                </div>
            </form>
            @endif
        </div>
    </div>
</div>
</div>
</div>


@include('prize-draws.winnerRevealModal')

@include('prize-draws.new-winner-Reveal-Modal',['draw' => $prizeDraw])



@endsection
