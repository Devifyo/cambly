@extends('layouts.student.app')

@section('title', 'Payment History')

@push('styles')
<style>
    .container-max { max-width: 1100px; margin: 0 auto; padding: 1rem; margin-top: 3.5rem; }
    .summary-row { display:flex; gap: 1rem; align-items:stretch; margin-bottom: 1.5rem; flex-wrap:wrap; justify-content:center; }
    .stat-card { background:#fff; border-radius:12px; padding:1.2rem; border:1px solid #e6edf6; text-align:center; flex:1 1 200px; box-shadow:0 2px 6px rgba(14,130,253,0.04); }
    .credits-number { font-weight:800; font-size:1.6rem; color:#0E82FD; }
    .filter-pills { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap; justify-content:center; }
    .filter-pill { padding:.45rem .95rem; border-radius:999px; border:1px solid #e6edf6; background:#fff; cursor:pointer; font-weight:600; }
    .filter-pill.active { background: linear-gradient(90deg,#0E82FD 0%,#06AED4 70%); color:#fff; border-color:transparent; }
    .tx-list { margin-top: 1rem; }
    .tx-item { display:flex; align-items:center; gap:1rem; background:#fff; border-radius:10px; padding:1rem; border:1px solid #eef6ff; margin-bottom:.75rem; }
    .tx-type { width:56px; height:56px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; color:#fff; }
    .tx-payment { background:#0E82FD; }
    .tx-refund { background:#10b981; }
    .tx-body { flex:1; }
    .tx-meta { color:#6b7280; font-size:0.9rem; }
    .btn { padding:.5rem .9rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; }
    .btn-ghost { background:#f3f4f6; color:#111827; }
    .btn-primary { background:#0E82FD; color:#fff; }
    .empty-state { text-align:center; padding:3rem; background: #fff; border-radius: 12px; border: 1px solid #eef6ff;}
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
    .empty-state p { font-size: 1.1rem; color: #6b7280; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="breadcrumb-bar overflow-visible">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb" style="justify-content:center; display:flex; gap:.5rem;">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="isax isax-home-15"></i></a></li>
                        <li class="breadcrumb-item">Account</li>
                        <li class="breadcrumb-item active">Payment History</li>
                    </ol>
                    <h2 class="breadcrumb-title">Payment History</h2>
                    <p class="text-muted" style="margin-top: 5px;">View your subscription charges and invoices</p>
                </nav>
            </div>
        </div>
        {{-- search filter --}}
        {{-- <div class="bg-primary-gradient rounded-pill doctors-search-box">
            <div class="search-box-one rounded-pill">
                <form method="GET" action="{{ route('student.account.payment-history') }}" id="searchForm">
                    <div class="search-input search-line">
                        <i class="isax isax-search-normal-1 bficon"></i>
                        <div class="mb-0">
                            <input 
                                type="text" 
                                name="q" 
                                class="form-control" 
                                placeholder="Search by  invoice ID"
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
                                id="payment_datepicker"
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
        {{-- end search filter --}}
    </div>

    <div class="breadcrumb-bg">
        <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
        <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
    </div>
</div>

<div class="container-max">
    {{-- summary --}}
    {{-- <div class="summary-row">
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Active Plan</div>
            <div class="credits-number" style="font-size: 1.4rem;">{{ $stats['plan'] ?? '—' }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Next Billing Date</div>
            <div class="credits-number" style="font-size: 1.4rem;">{{ $stats['next_billing'] ?? '—' }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size:.85rem; color:#6b7280;">Total Billed</div>
            <div class="credits-number" style="color:#111827;">{{ $stats['total_billed'] ?? '—' }}</div>
        </div>
    </div> --}}
    {{-- end summary --}}
    {{-- filter pills --}}
    {{-- <div class="filter-pills" id="filterPills">
        <div class="filter-pill active" data-filter="all">All</div>
        <div class="filter-pill" data-filter="subscription">Subscription</div>
        <div class="filter-pill" data-filter="refund">Refunds</div>
    </div> --}}
    {{-- end filter pills --}}

    <div class="tx-list" id="txList">
        
        
        @forelse ($payments as $tx)
            <div class="tx-item" data-type="{{ $tx->data_type }}">
                
                <div class="tx-type {{ $tx->icon_class }}">
                    {{-- Use the accessor for the symbol --}}
                    {!! $tx->type === 'refund' ? '+' : $tx->currency_symbol_accessor !!}
                </div>

                <div class="tx-body">
                    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                        <div>
                            {{-- Use the new simple accessors --}}
                            <div style="font-weight:700;">{{ $tx->invoice_description }}</div>
                            <div class="tx-meta">Invoice #{{ $tx->invoice_number }}</div>
                        </div>

                        <div style="text-align:right;">
                            <div style="font-weight:800; font-size:1.05rem;">
                                {{ $tx->formatted_amount }}
                            </div>
                            @if(strtolower($tx->formatted_status) === 'paid' || strtolower($tx->formatted_status) === 'succeeded')
                                <span class="badge" style="background-color: #e0fcf4; color: #10b981;">{{ $tx->formatted_status }}</span>
                            @else
                                <span class="badge" style="background-color: #fff8e1; color: #f97316;">{{ $tx->formatted_status }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="margin-top:.5rem; color:#6b7280; font-size:.88rem;">
                        Billed on: {{ $tx->formatted_date }}
                    </div>
                </div>
                {{-- view invoice --}}
                {{-- <div>
                    @if($tx->invoice_pdf)
                        <a href="{{ $tx->invoice_pdf }}" target="_blank" class="btn btn-ghost" style="white-space:nowrap;">
                            View Invoice
                        </a>
                    @else
                        <a href="#" class="btn btn-ghost disabled" aria-disabled="true" style="white-space:nowrap;">
                            No Invoice
                        </a>
                    @endif
                </div> --}}
            </div>
        @empty
            <div class="empty-state">
                <i class="isax isax-wallet-2"></i>
                <p>No payment history found.</p>
            </div>
        @endforelse
            <div class="col-md-12">
                <x-pagination :paginator="$payments" />
            </div>
    </div>
</div>
@endsection

@push('scripts')


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();

            if ($('#payment_datepicker').length) {
                var defaultDate = @json(request('date') ?: null);
                $('#payment_datepicker').datetimepicker({
                    format: 'YYYY-MM-DD',
                    showClose: true,
                    showClear: true,
                    defaultDate: defaultDate || false,
                    icons: {
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right'
                    }
                });
            }

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
        });
    </script>
@endpush