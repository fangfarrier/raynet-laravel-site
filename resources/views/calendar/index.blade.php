@extends('layouts.app')

@section('title', 'Calendar')

@section('content')

    {{-- Small CSS helpers for hover cards --}}
    <style>
        .lr-cal-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.18rem;
        }
        .lr-cal-week {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.18rem;
            margin-bottom: 0.18rem;
        }
        .lr-cal-day {
            position: relative;
            min-height: 70px;
            padding: 0.35rem 0.35rem 0.4rem;
            border-radius: 0.55rem;
            border: 1px solid rgba(31,41,55,0.95);
            background: radial-gradient(circle at top, #020617, #020617 55%, #020617 100%);
            font-size: 0.78rem;
            overflow: hidden;
        }
        .lr-cal-day.outside {
            opacity: 0.45;
        }
        .lr-cal-day.today {
            border-color: rgba(56,189,248,0.9);
            box-shadow: 0 0 0 1px rgba(56,189,248,0.7);
        }
        .lr-cal-date {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
            font-size: 0.78rem;
        }
        .lr-cal-event {
            position: relative;
            margin-bottom: 0.16rem;
            padding: 0.16rem 0.35rem;
            border-radius: 999px;
            background: rgba(15,23,42,0.95);
            border: 1px solid rgba(148,163,184,0.5);
            font-size: 0.72rem;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .lr-cal-event-span-start {
            border-top-left-radius: 999px;
            border-bottom-left-radius: 999px;
        }
        .lr-cal-event-span-middle {
            border-radius: 0;
        }
        .lr-cal-event-span-end {
            border-top-right-radius: 999px;
            border-bottom-right-radius: 999px;
        }
        .lr-cal-event-pop {
            display: none;
            position: absolute;
            z-index: 30;
            left: 0;
            top: 110%;
            min-width: 210px;
            max-width: 260px;
            background: #020617;
            border-radius: 0.6rem;
            border: 1px solid rgba(148,163,184,0.8);
            padding: 0.5rem 0.6rem;
            box-shadow: 0 16px 40px rgba(0,0,0,0.85);
        }
        .lr-cal-event:hover .lr-cal-event-pop {
            display: block;
        }
    </style>

    {{-- Header / controls --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:1.1rem;">
        <div>
            <h1 style="margin:0; font-size:1.35rem; color:#e5e7eb;">
                {{ $monthName }} {{ $year }}
            </h1>
            <p style="margin:0.25rem 0 0; font-size:0.85rem; color:#9ca3af;">
                Group training, exercises and event support across Liverpool & Merseyside.
            </p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:0.5rem; justify-content:flex-end;">
            {{-- Prev / next month --}}
            <div style="display:inline-flex; align-items:center; gap:0.25rem; font-size:0.85rem;">
                <a href="{{ route('calendar', ['year' => $prevYear, 'month' => sprintf('%02d', $prevMonth)]) }}"
                   style="padding:0.25rem 0.6rem; border-radius:999px; border:1px solid rgba(148,163,184,0.7);
                          color:#e5e7eb; text-decoration:none;">
                    ‹ {{ \Illuminate\Support\Carbon::createFromDate($prevYear, $prevMonth, 1)->format('M') }}
                </a>
                <a href="{{ route('calendar', ['year' => $nextYear, 'month' => sprintf('%02d', $nextMonth)]) }}"
                   style="padding:0.25rem 0.6rem; border-radius:999px; border:1px solid rgba(148,163,184,0.7);
                          color:#e5e7eb; text-decoration:none;">
                    {{ \Illuminate\Support\Carbon::createFromDate($nextYear, $nextMonth, 1)->format('M') }} ›
                </a>
            </div>

            {{-- Export month as ICS --}}
            <a href="{{ route('calendar.ics', ['year' => $year, 'month' => sprintf('%02d', $currentMonth->month)]) }}"
               style="padding:0.3rem 0.9rem; border-radius:999px;
                      border:1px solid rgba(56,189,248,0.9); color:#e5e7eb;
                      text-decoration:none; font-size:0.8rem;">
                Export this month (.ics)
            </a>
        </div>
    </div>

    {{-- Weekday headings --}}
    <div class="lr-cal-grid" style="margin-bottom:0.25rem; font-size:0.75rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.12em;">
        @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayName)
            <div style="padding:0.2rem 0.2rem 0.25rem;">
                {{ $dayName }}
            </div>
        @endforeach
    </div>

    {{-- Month grid --}}
    @foreach ($weeks as $week)
        <div class="lr-cal-week">
            @foreach ($week as $day)
                @php
                    /** @var \Illuminate\Support\Carbon $date */
                    $date = $day['date'];
                    $isCurrent = $day['isCurrentMonth'];
                    $isToday   = $day['isToday'];
                    $events    = $day['events'];
                    $dayKey    = $date->toDateString();
                @endphp

                <div class="lr-cal-day {{ $isCurrent ? '' : 'outside' }} {{ $isToday ? 'today' : '' }}">
                    {{-- Date number --}}
                    <div class="lr-cal-date">
                        <span style="font-weight:600; color:#e5e7eb;">
                            {{ $date->day }}
                        </span>
                        @if ($isToday)
                            <span style="font-size:0.7rem; color:#93c5fd;">Today</span>
                        @endif
                    </div>

                    {{-- Events as badges --}}
                    @foreach ($events as $item)
                        @php
                            /** @var \App\Models\Event $eventModel */
                            $eventModel = $item['event'];
                            $spanPart   = $item['span']; // single|start|middle|end

                            $type       = $eventModel->type;
                            $colour     = $type && $type->colour ? $type->colour : '#0ea5e9';
                            $spanClass  = match ($spanPart) {
                                'start'  => 'lr-cal-event-span-start',
                                'middle' => 'lr-cal-event-span-middle',
                                'end'    => 'lr-cal-event-span-end',
                                default  => '',
                            };
                        @endphp

                        <div class="lr-cal-event {{ $spanClass }}"
                             style="border-left: 3px solid {{ $colour }};">
                            <span style="color:#e5e7eb;">
                                {{ $eventModel->title }}
                            </span>

                            {{-- Hover card --}}
                            <div class="lr-cal-event-pop">
                                <div style="font-weight:600; margin-bottom:0.2rem; color:#e5e7eb;">
                                    {{ $eventModel->title }}
                                </div>
                                <div style="font-size:0.75rem; color:#9ca3af; margin-bottom:0.15rem;">
                                    {{ $eventModel->displayDate() }}
                                    @if ($eventModel->ends_at)
                                        – {{ $eventModel->ends_at->format('D j M Y, H:i') }}
                                    @endif
                                </div>
                                @if ($eventModel->location)
                                    <div style="font-size:0.75rem; color:#9ca3af; margin-bottom:0.15rem;">
                                        📍 {{ $eventModel->location }}
                                    </div>
                                @endif
                                @if ($type)
                                    <div style="font-size:0.75rem; color:#a5b4fc;">
                                        Type: {{ $type->name }}
                                    </div>
                                @endif
                                <a href="{{ $eventModel->url() }}"
                                   style="display:inline-block; margin-top:0.3rem; font-size:0.75rem;
                                          color:#93c5fd; text-decoration:none;">
                                    View event →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- Link to list view --}}
    <div style="margin-top:1.25rem; font-size:0.85rem;">
        <a href="{{ route('events.index') }}"
           style="color:#93c5fd; text-decoration:none;">
            View full list of upcoming events →
        </a>
    </div>

@endsection