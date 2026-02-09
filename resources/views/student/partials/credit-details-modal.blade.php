<div class="modal fade" id="creditDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1">Tickets Details</h5>
                    <p class="text-muted small mb-0">Breakdown of your currently active tickets and expiration dates.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3" style="max-height: 70vh; overflow-y: auto;">
                
                {{-- SECTION 1: Subscription Tickets --}}
                @if(!empty($currentCredits['tickets_added_by_subscription']))
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge p-2 me-2 rounded-circle" style="background-color: rgba(4, 189, 108, 0.1); color: #04bd6c;">
                                <i class="fa-solid fa-repeat"></i>
                            </span>
                            <h6 class="text-uppercase text-muted small fw-bold mb-0">Subscription Plan</h6>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($currentCredits['tickets_added_by_subscription'] as $batch)
                                <div class="ticket-card p-3 rounded-3 border bg-light position-relative overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Monthly Plan</h6>
                                            <small class="text-muted d-block">
                                                Issued: {{ \Carbon\Carbon::parse($batch['created_at'])->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge rounded-pill px-3 py-2" 
                                                  style="background-color: {{ $batch['available_tickets'] > 0 ? '#04bd6c' : '#6c757d' }};">
                                                {{ $batch['available_tickets'] }} Available
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Progress Bar: Already Green --}}
                                    @php 
                                        $total = $batch['total_tickets'];
                                        $avail = $batch['available_tickets'];
                                        $percent = $total > 0 ? (($total - $avail) / $total) * 100 : 100;
                                        $width = 100 - $percent; 
                                    @endphp
                                    <div class="progress my-2" style="height: 6px; background-color: #e9ecef;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $width }}%; background-color: #04bd6c;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($batch['days_left'] <= 5)
                                                <span class="text-danger small fw-bold">
                                                    <i class="fa-regular fa-clock me-1"></i> Expires in {{ $batch['days_left'] }} days
                                                </span>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="fa-regular fa-calendar me-1"></i> Expires: {{ \Carbon\Carbon::parse($batch['expiring_on'])->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            Total: <strong>{{ $total }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SECTION 2: Admin/Manual Tickets --}}
                @if(!empty($currentCredits['tickets_added_by_admin']))
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-purple-light text-purple p-2 me-2 rounded-circle">
                                <i class="fa-solid fa-gift"></i>
                            </span>
                            <h6 class="text-uppercase text-muted small fw-bold mb-0">Bonus & Manual Additions</h6>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($currentCredits['tickets_added_by_admin'] as $batch)
                                <div class="ticket-card p-3 rounded-3 border bg-light position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">
                                                {{ $batch['reason'] ?? 'Manual Addition' }}
                                            </h6>
                                            <small class="text-muted d-block">
                                                Issued: {{ \Carbon\Carbon::parse($batch['created_at'])->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            {{-- Optional: Changed badge to Green as well for consistency, change back to bg-purple if needed --}}
                                            <span class="badge text-white rounded-pill px-3 py-2" style="background-color: #04bd6c;">
                                                {{ $batch['available_tickets'] }} Available
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Progress Bar: UPDATED TO #04bd6c --}}
                                    @php 
                                        $total = $batch['total_tickets'];
                                        $avail = $batch['available_tickets'];
                                        $percent = $total > 0 ? (($total - $avail) / $total) * 100 : 100;
                                    @endphp
                                    <div class="progress my-2" style="height: 6px; background-color: #e9ecef;">
                                        {{-- Removed 'bg-purple' class and added inline style for color --}}
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ 100 - $percent }}%; background-color: #04bd6c;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($batch['days_left'] <= 5)
                                                <span class="text-danger small fw-bold">
                                                    <i class="fa-regular fa-clock me-1"></i> Expires in {{ $batch['days_left'] }} days
                                                </span>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="fa-regular fa-calendar me-1"></i> Expires: {{ \Carbon\Carbon::parse($batch['expiring_on'])->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            Total: <strong>{{ $total }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Empty State --}}
                @if(empty($currentCredits['tickets_added_by_subscription']) && empty($currentCredits['tickets_added_by_admin']))
                    <div class="text-center py-5">
                        <div class="mb-3 text-muted opacity-50"><i class="fa-solid fa-wallet fa-3x"></i></div>
                        <h6 class="text-muted">No active tickets found</h6>
                        <p class="small text-muted">Purchase a subscription to get started.</p>
                    </div>
                @endif
            </div>
            
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 w-100 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Scoped styles for this modal */
    .bg-purple { background-color: #7950f2 !important; }
    .bg-purple-light { background-color: #f3f0ff !important; }
    
    /* Updated .text-purple to use the green color as requested in your previous snippet */
    .text-purple { color: #04bd6c !important; }
    
    /* Added a specific utility class for your green if needed elsewhere */
    .bg-custom-green { background-color: #04bd6c !important; }
    .text-custom-green { color: #04bd6c !important; }

    .ticket-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #e9ecef !important;
    }
    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-color: #dee2e6 !important;
        background-color: #fff !important;
    }
</style>