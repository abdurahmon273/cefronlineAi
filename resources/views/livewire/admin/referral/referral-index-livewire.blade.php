<div class="card">
    <div class="card-header">
        Referral
    </div>

    <div class="card-body">
        <div wire:loading.remove wire:target="payReferral">
        <div class="row" x-data="{ open: @entangle('dropdownOpen').live }" @click.away="open = false">
            <div class="col-md-6 form-group position-relative">
                <label for="referral-user-search" class="required">User</label>
                <input
                    id="referral-user-search"
                    type="text"
                    wire:model.live.debounce.350ms="search"
                    wire:focus="openDropdown"
                    x-on:focus="open = true"
                    class="form-control"
                    placeholder="Search user by name or phone number">

                <div
                    x-cloak
                    x-show="open"
                    class="position-absolute w-100 bg-white border rounded shadow-sm mt-1"
                    style="z-index: 1050; max-height: 420px; overflow: hidden;">
                    <div class="p-2 border-bottom text-muted small d-flex justify-content-between align-items-center">
                        <span>Search results</span>
                        <span wire:loading wire:target="search" class="small">Loading...</span>
                    </div>

                    <div style="max-height: 320px; overflow-y: auto;">
                        @forelse($users as $user)
                            @php
                                $userLabel = trim(($user->name ?? '') . ' ' . ($user->surname ?? '')) ?: 'User #' . $user->id;
                            @endphp
                            <button
                                type="button"
                                wire:click="selectUser({{ $user->id }})"
                                x-on:click="open = false"
                                class="btn btn-link text-left text-decoration-none text-dark w-100 px-3 py-2 border-0 border-bottom">
                                <div class="font-weight-bold">{{ $userLabel }}</div>
                                <div class="small text-muted">{{ $user->mobil_number ?: 'No phone number' }}</div>
                            </button>
                        @empty
                            <div class="px-3 py-3 text-muted small">
                                No users found
                            </div>
                        @endforelse
                    </div>

                    @if($users->hasPages())
                        <div class="p-2 border-top">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-3 form-group position-relative">
                <label for="referral-user-search" class="required">Start Date</label>
                <input type="text" id="start_date" class="form-control" placeholder="Start Date" wire:model.lazy="start_date">
            </div>
            <div class="col-md-3 form-group position-relative">
                <label for="referral-user-search" class="required">End Date</label>
                    <input type="text" id="end_date" class="form-control" placeholder="End Date" wire:model.lazy="end_date">
            </div>
        </div>

        @if($selectedUserId)
            <div wire:loading.remove wire:target="selectUser" class="row mt-3">
                <div class="col-12">
                    @if($loadError)
                        <div class="alert alert-danger mb-0">
                            {{ $loadError }}
                        </div>
                    @else
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
                                    <div>
                                        <div class="text-muted small text-uppercase mb-2">Selected User</div>
                                        <h4 class="mb-1">{{ $selectedUserLabel }}</h4>
                                        <div class="small text-muted">{{ $referralLink }}</div>
                                    </div>

                                    @if($referralBalance > 0)
                                        <button
                                            type="button"
                                            wire:click="payReferral"
                                            wire:loading.attr="disabled"
                                            wire:target="payReferral"
                                            class="btn btn-success mt-3 mt-lg-0 px-4">
                                            Pay
                                        </button>
                                    @else
                                        <span class="badge badge-secondary mt-3 mt-lg-0 px-3 py-2">
                                            Not available
                                        </span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Plan</div>
                                            <div class="h5 mb-0">
                                                {{ $hasPremiumPrice && $plan !== null ? $plan . ' ' : 'No premium plan' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Referral Percentage</div>
                                            <div class="h5 mb-0">{{ number_format($percentage, 0, '.', '') }}%</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Active Referrals</div>
                                            <div class="h5 mb-0">{{ $activeReferralCount }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Available Balance</div>
                                            <div class="h5 mb-0 text-primary">{{ number_format($referralBalance, 0, '.', ' ') }} UZS</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Paid Balance</div>
                                            <div class="h5 mb-0 text-success">{{ number_format($paidReferralBalance, 0, '.', ' ') }} UZS</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="text-muted small text-uppercase mb-2">Total Balance</div>
                                            <div class="h5 mb-0 text-dark">{{ number_format($totalReferralBalance, 0, '.', ' ') }} UZS</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded p-3 bg-white">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                                        <div>
                                            <div class="h5 mb-0">Referral Users</div>
                                        </div>
                                        <div class="small text-muted mt-2 mt-md-0">
                                            Showing {{ count($recentReferrals) }} / {{ $referralCount }}
                                        </div>
                                    </div>

                                    @if(count($recentReferrals))
                                        <div class="d-flex flex-column" style="gap: 12px;">
                                            @foreach($recentReferrals as $referral)
                                                @php
                                                    $status = strtolower((string) ($referral['status'] ?? 'active'));
                                                    $isPaid = $status === 'paid';
                                                    $discount = (float) ($referral['discount'] ?? 0);
                                                @endphp

                                                <div class="border rounded px-3 py-2">
                                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                                                        <div class="mb-2 mb-lg-0 pr-lg-3">
                                                            <div class="font-weight-bold mb-1">{{ $referral['fullname'] ?? 'User' }}</div>
                                                            <div class="small text-muted">
                                                                {{ $referral['phone'] ?: 'No phone number' }}
                                                            </div>
                                                        </div>

                                                        <div class="d-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 16px;">
                                                            <div class="text-lg-right">
                                                                <div class="font-weight-bold">{{ $referral['plan'] ?: 'No premium plan' }}</div>
                                                                <div class="small text-muted">
                                                                    {{ $referral['purchase_date'] ? \Carbon\Carbon::parse($referral['purchase_date'])->format('j M Y') : 'Premium purchase not found' }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                @if($discount > 0)
                                                                    <div class="h6 mb-0 text-dark">{{ number_format($referral['net_price'] ?? 0, 0, '.', ' ') }} UZS</div>
                                                                    <div class="small text-muted">
                                                                        <span class="mr-2">{{ number_format($discount, 0, '.', '') }}%</span>
                                                                        <span style="text-decoration: line-through;">{{ number_format($referral['original_price'] ?? 0, 0, '.', ' ') }} UZS</span>
                                                                    </div>
                                                                @else
                                                                    <div class="h6 mb-0 text-dark">{{ number_format($referral['net_price'] ?? 0, 0, '.', ' ') }} UZS</div>
                                                                @endif
                                                            </div>

                                                            <div>
                                                                <div class="font-weight-bold {{ $isPaid ? 'text-success' : 'text-primary' }} ">{{ number_format($referral['referral_profit'] ?? 0, 0, '.', ' ') }} UZS</div>
                                                            </div>

                                                            <div class="d-inline-flex align-items-center px-2 py-1 rounded-pill small font-weight-bold {{ $isPaid ? 'bg-success text-white' : 'bg-primary text-white' }}">
                                                                {{ $isPaid ? 'Paid' : 'Active' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($referralCount > count($recentReferrals))
                                            <div class="mt-3 text-center">
                                                <button
                                                    type="button"
                                                    wire:click="showMoreReferrals"
                                                    wire:loading.attr="disabled"
                                                    wire:target="showMoreReferrals"
                                                    class="btn btn-outline-primary px-4">
                                                    <span wire:loading.remove wire:target="showMoreReferrals">More</span>
                                                    <span wire:loading wire:target="showMoreReferrals">Loading...</span>
                                                </button>
                                            </div>
                                        @endif
                                    @else
                                        <div class="border rounded p-4 text-center text-muted">
                                            Referral users not found
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div wire:loading.flex wire:target="selectUser" class="mt-3 align-items-center text-muted">
                Loading...
            </div>
        @endif
        </div>

        <div wire:loading.flex wire:target="payReferral" class="align-items-center justify-content-center" style="min-height: 360px;">
            <div class="text-center text-muted">
                <div class="spinner-border text-success mb-3" role="status"></div>
                <div>Loading...</div>
            </div>
        </div>
    </div>
</div>
