<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Weekly Schedule {{ $startOfWeek->format('d/m/Y') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d/m/Y')
        }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 15px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 13px;
            text-align: center;
            margin-bottom: 0;
            font-weight: bold;
        }

        .sub-header {
            font-size: 11px;
            text-align: center;
            margin: 2px 0 10px 0;
        }

        h2 {
            font-size: 11px;
            margin: 15px 0 5px 0;
            padding: 6px;
            background: #e5e5e5;
            border: 1px solid #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 5px 6px;
            vertical-align: top;
            font-size: 8px;
            text-transform: uppercase;
        }

        th {
            background: #ddd;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        td {
            word-wrap: break-word;
            height: 112px;
            text-align: center;
        }

        .room {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
        }

        .room-session {
            font-size: 8px;
            text-align: center;
            margin-top: 8px;
        }

        .room-breakout {
            font-size: 7px;
            text-align: center;
            margin-top: 4px;
        }

        .party-name {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            padding-bottom: 14px;
            margin-top: 8px;
        }

        .party-solicitor {
            font-size: 7px;
            text-align: center;
        }

        .arbitrator {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
        }

        .additional {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <h1>ASIAN INTERNATIONAL ARBITRATION CENTRE | OPERATIONS DEPARTMENT</h1>
    <p class="sub-header">WEEKLY SCHEDULE: {{ $startOfWeek->format('d/m/Y') }} - {{
        $startOfWeek->copy()->endOfWeek()->format('d/m/Y') }}</p>

    @foreach($weekDays as $day)
    @php $dayStr = $day->toDateString(); @endphp
    <h2>{{ strtoupper($day->format('l d/m/Y')) }}</h2>

    @php
    $dayBookings = $allBookings[$dayStr] ?? collect();
    $dayEvents = $allEvents[$dayStr] ?? collect();
    $hasData = $dayBookings->isNotEmpty() || $dayEvents->isNotEmpty();
    @endphp

    @if($hasData)
    <table>
        <thead>
            <tr>
                <th style="width:10%">ROOM</th>
                <th style="width:25%">CLAIMANT &amp; SOLICITOR</th>
                <th style="width:15%">ARBITRATOR</th>
                <th style="width:25%">RESPONDENT &amp; SOLICITOR</th>
                <th style="width:25%">ADDITIONAL</th>
            </tr>
        </thead>
        <tbody>
            {{-- Hearings --}}
            @foreach($dayBookings as $b)
            @php
            $cl = $b->participants->where('role','claimant')->first();
            $clSol = $b->participants->where('role','claimant_solicitor')->first();
            $res = $b->participants->where('role','respondent')->first();
            $resSol = $b->participants->where('role','respondent_solicitor')->first();
            $arbs =
            $b->participants->whereIn('role',['presiding_arbitrator','co_arbitrator'])->pluck('contact.name')->implode(',
            ');
            $feats = $b->features->pluck('name')->implode(', ');
            $sessionLabel = match($b->session_type) { 'full_day' => 'FULL DAY', 'half_am' => 'AM', 'half_pm' => 'PM',
            default => '' };
            $vcr = $b->features->contains(fn($f) => in_array($f->name, ['VC', 'Recording'])) ? 'VCR' : '';
            @endphp
            <tr>
                <td>
                    <div class="room">{{ $b->room->room_code }}</div>
                    <div class="room-session">{{ $vcr ?: $sessionLabel }}</div>
                    @if($b->breakoutRooms->isNotEmpty())
                    <div class="room-breakout">BR: {{ $b->breakoutRooms->pluck('room.room_code')->implode(', ') }}</div>
                    @endif
                </td>
                <td>
                    <div class="party-name">{{ strtoupper($cl?->contact?->name ?? '-') }}</div>
                    <div class="party-solicitor">{{ strtoupper($clSol?->contact?->name ?? '-') }}</div>
                </td>
                <td>
                    <div class="arbitrator">{{ strtoupper($arbs ?: '-') }}</div>
                </td>
                <td>
                    <div class="party-name">{{ strtoupper($res?->contact?->name ?? '-') }}</div>
                    <div class="party-solicitor">{{ strtoupper($resSol?->contact?->name ?? '-') }}</div>
                </td>
                <td>
                    <div class="additional">{{ strtoupper($feats ?: '-') }}</div>
                </td>
            </tr>
            @endforeach

            {{-- Events --}}
            @foreach($dayEvents as $e)
            @php $sessionLabel = $e->start_time < '12:00:00' ? 'AM' : 'PM' ; @endphp <tr>
                <td>
                    <div class="room">{{ $e->room->room_code }}</div>
                    <div class="room-session">{{ $sessionLabel }}</div>
                </td>
                <td>
                    <div class="party-name">EVENT</div>
                </td>
                <td>
                    <div class="arbitrator">{{ strtoupper($e->event_name) }}</div>
                </td>
                <td></td>
                <td>
                    <div class="additional">{{ strtoupper($e->notes ?: '-') }}</div>
                </td>
                </tr>
                @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #999;">NO HEARINGS OR EVENTS SCHEDULED.</p>
    @endif

    @if(!$loop->last)<div class="page-break"></div>@endif
    @endforeach
</body>

</html>