@extends('layouts.student.app')

@section('title', 'Ticket History')

@push('styles')
    <style>
        /* This CSS is from your original file. 
           The new structure should work well with it, 
           but you may need to tweak styles for 'doctors-search-box' if needed.
        */
        .container-max { max-width: 1100px; margin: 0 auto; padding: 1rem; margin-top: 3.5rem; } /* Adjusted margin-top */

        /* Stats cards */
        .summary-row { display:flex; gap: 1rem; align-items:stretch; margin-bottom: 1.5rem; flex-wrap:wrap; justify-content:center; }
        .stat-card { background:#fff; border-radius:12px; padding:1.2rem; border:1px solid #e6edf6; text-align:center; flex:1 1 200px; box-shadow:0 2px 6px rgba(14,130,253,0.04); }
        .credits-number { font-weight:800; font-size:1.6rem; color:#0E82FD; }

        /* Filter pills */
        .filter-pills { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap; justify-content:center; }
        .filter-pill { padding:.45rem .95rem; border-radius:999px; border:1px solid #e6edf6; background:#fff; cursor:pointer; font-weight:600; }
        .filter-pill.active { background: linear-gradient(90deg,#0E82FD 0%,#06AED4 70%); color:#fff; border-color:transparent; }

        /* Transactions */
        .tx-list { margin-top: 1rem; }
        .tx-item { display:flex; align-items:center; gap:1rem; background:#fff; border-radius:10px; padding:1rem; border:1px solid #eef6ff; margin-bottom:.75rem; }
        .tx-type { width:56px; height:56px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; color:#fff; }
        
        .tx-issued { background:#10b981; } .tx-debt { background:#0E82FD; }
        .tx-refund { background:#f97316; } .tx-hold { background:#eab308; }
        .tx-other { background:#6b7280; }

        .tx-body { flex:1; }
        .tx-meta { color:#6b7280; font-size:0.9rem; }
        .btn { padding:.5rem .9rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; }
        .btn-ghost { background:#f3f4f6; color:#111827; }
        .btn-primary { background:#0E82FD; color:#fff; }

        /* Empty state */
        .empty-state { text-align:center; padding:3rem; background: #fff; border-radius: 12px; border: 1px solid #eef6ff;}
        .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
        .empty-state p { font-size: 1.1rem; color: #6b7280; font-weight: 500; }

        /* Minor adjustments for new search bar */
        /* .doctors-search-box { margin-top: 1.5rem; }
        .search-box-one { padding: 0.5rem; }
        .breadcrumb-bar .search-box-one .form-control { border: none; } */
    </style>
@endpush

@section('content')
{{-- breadcrumb start --}}
<div class="breadcrumb-bar overflow-visible">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb" style="justify-content:center; display:flex; gap:.5rem;">
                        <li class="breadcrumb-item"><a href="/"><i class="isax isax-home-15"></i></a></li>
                        <li class="breadcrumb-item">Account</li>
                        <li class="breadcrumb-item active">Ticket History</li>
                    </ol>
                    <h2 class="breadcrumb-title">Ticket History</h2>
                    <p class="text-muted" style="margin-top: 5px;">View your credit usage and transactions</p>
                </nav>
            </div>
        </div>
        {{--  --}}
        {{-- <div class="bg-primary-gradient rounded-pill doctors-search-box">
            <div class="search-box-one rounded-pill">
                
                <form method="GET" action="{{ route('student.account.ticket-history') }}" id="searchForm">
                    <div class="search-input search-line">
                        <i class="isax isax-search-normal-1 bficon"></i>
                        <div class="mb-0">
                            <input 
                                type="text" 
                                name="q" 
                                class="form-control" 
                                placeholder="Search transactions or description"
                                value="{{ request('q') }}"
                            >
                        </div>
                    </div>

                    <div class="search-input search-calendar-line">
                        <i class="isax isax-calendar-tick5"></i>
                        <div class="mb-0">
                            <input 
                                type="text" 
                                name="date" 
                                id="ticket_datepicker"  
                                class="form-control datetimepicker" 
                                placeholder="Filter by date (YYYY-MM-DD)"
                                autocomplete="off"
                                value="{{ request('date') }}"
                            >
                        </div>
                    </div>

                    <div class="form-search-btn">
                        <button class="btn btn-primary d-inline-flex align-items-center rounded-pill" type="submit">
                            <i class="isax isax-search-normal-15 me-2"></i>Search
                        </button>
                    </div>
                </form>
                </div>
        </div> --}}
        {{--  --}}
    </div>
    <div class="breadcrumb-bg">
        <img src="{{asset('assets/img/bg/breadcrumb-bg-01.png')}}" alt="img" class="breadcrumb-bg-01">
        <img src="{{asset('assets/img/bg/breadcrumb-bg-02.png')}}" alt="img" class="breadcrumb-bg-02">
    </div>
</div>
{{-- breadcrumb end --}}

<div class="container-max">

    {{-- <div class="summary-row">
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Total Earned</div>
            <div class="credits-number" style="color:#10b981;">+{{ $stats['earned'] }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Used</div>
            <div class="credits-number" style="color:#dc2626;">-{{ $stats['used'] }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Remaining</div>
            <div class="credits-number">{{ $stats['remaining'] }}</div>
        </div>
    </div> --}}

    <div class="filter-pills" id="filterPills">
        <div class="filter-pill active" data-filter="all">All</div>
        <div class="filter-pill" data-filter="issued">Issued</div>
        <div class="filter-pill" data-filter="debt">Used</div>
        <div class="filter-pill" data-filter="refund">Refunded</div>
        <div class="filter-pill" data-filter="other">Other</div>
    </div>

    <div class="tx-list" id="txList">
        @forelse ($ticketHistory as $tx)
            
            @php
                // Logic to determine display properties
                $title = 'Transaction'; $iconClass = 'tx-other'; $sign = '';
                $amount = (int)$tx->credits; $dataType = 'other';

                switch ($tx->type) {
                    case 'issued':
                        $title = 'Monthly Credits Issued'; $iconClass = 'tx-issued';
                        $sign = '+'; $dataType = 'issued';
                        break;
                    case 'debt':
                        $title = 'Booking Confirmed'; $iconClass = 'tx-debt';
                        $sign = '-'; $dataType = 'debt';
                        break;
                    case 'refund':
                        $title = 'Booking Cancelled (Refunded)'; $iconClass = 'tx-refund';
                        $sign = '+'; $dataType = 'refund';
                        break;
                    case 'no_refund':
                        $title = 'Booking Cancelled (No Refund)'; $iconClass = 'tx-other';
                        $sign = ''; $amount = 0; $dataType = 'other';
                        break;
                    case 'hold':
                        $title = 'Booking On Hold'; $iconClass = 'tx-hold';
                        $sign = '-'; $dataType = 'other';
                        break;
                }
            @endphp

            <div class="tx-item" data-type="{{ $dataType }}">
                <div class="tx-type {{ $iconClass }}">
                    {{ $sign == '' ? $amount : $sign }}
                </div>
                <div class="tx-body">
                    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                        <div>
                            <div style="font-weight:700;">{{ $title }}</div>
                            <div class="tx-meta">{{ $tx->description }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800; font-size:1.05rem;">
                                {{ $sign }}{{ $amount }}
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:.5rem; color:#6b7280; font-size:.88rem;">
                        {{ \Carbon\Carbon::parse($tx->created_at)->format('M j, Y • g:i A') }} • Ref #{{ $tx->reference }}
                    </div>
                </div>
            </div>
        
        @empty
            <div class="empty-state">
                <i class="isax isax-ticket"></i>
                <p>No ticket transactions found.</p>
            </div>
        @endforelse
        <div class="col-md-12">
            <x-pagination :paginator="$ticketHistory" />
        </div>
    </div>
    
    {{-- <div style="margin-top: 2rem;">
        {{ $ticketHistory->links() }}
    </div> --}}

</div>
@endsection

@push('scripts')

    <script>
        feather.replace();

        (function () {
            // datepicker for hero
            if ($('#ticket_datepicker').length) {
                // var defaultDate = @json(request('date')) || false;
                $('#ticket_datepicker').datetimepicker({
                    format: 'YYYY-MM-DD',
                    showClose: true,
                    showClear: true,
                    // defaultDate: defaultDate,
                    icons: {
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right'
                    }
                });
            }

            // Filter pills (This JS logic remains the same)
            $('#filterPills').on('click', '.filter-pill', function () {
                var $p = $(this);
                $('#filterPills .filter-pill').removeClass('active');
                $p.addClass('active');

                var filter = $p.data('filter');
                if (filter === 'all') {
                    $('#txList .tx-item').show();
                } else {
                    $('#txList .tx-item').hide();
                    $('#txList .tx-item[data-type="' + filter + '"]').show();
                }
            });
        })();
    </script>
@endpush