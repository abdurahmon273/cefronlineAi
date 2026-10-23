@php use Illuminate\Support\Carbon; @endphp
<div class="card">
    <div class="card-header">
        Dashboard
    </div>

    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.3.1/css/all.min.css" rel="stylesheet">
        <body class="bg-default">
        <div class="main-content">
            <div class="header ">
                <div class="container-fluid">
                    <div class="header-body">
                        <div class="row mb-2">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="w-40 me-3">
                                    <select wire:model.live="FilterYear" class="form-control">
                                        @foreach (range(now()->year - 2, now()->year) as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class=" ml-2 w-40 me-3">
                                    <select wire:model.live="FilterMonth"  class="form-control">
                                        {{--                            <option value="0">Barcha oylar</option>--}}
                                        <option value="1">Yanvar</option>
                                        <option value="2">fevral</option>
                                        <option value="3">mart</option>
                                        <option value="4">aprel</option>
                                        <option value="5">may</option>
                                        <option value="6">iyun</option>
                                        <option value="7">iyul</option>
                                        <option value="8">avgust</option>
                                        <option value="9">sentyabr</option>
                                        <option value="10">oktyabr</option>
                                        <option value="11">noyabr</option>
                                        <option value="12">dekabr</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Jami foydalanuvchilar soni</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$UserCount}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Shu oy ichida royhatdan o'tganlar soni</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$OneMonthUserCount}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Shu yil ichida royhatdan otganlar soni</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$OneYearUserCount}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row mt-3">
                            <div class="col-xl-6 col-lg-6">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Jami Premium </h5>
                                                <span class="h2 font-weight-bold mb-0">{{$allPremiumSum}} </span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                                    <i class="fas fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Jami Kredit</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$allCreditSum}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                                    <i class="fas fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row mt-2">

                            <div class="col-xl-6 col-lg-6">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Jami Tolangan Referral</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$allPaidReferralSum}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                    <i class="fas fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="card card-stats mb-4 mb-xl-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title text-uppercase text-muted mb-0">Jami Tolanmagan Referat</h5>
                                                <span class="h2 font-weight-bold mb-0">{{$allReferralSum}}</span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                    <i class="fas fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>


                        <div class="row pt-5">


                            <div class="col-6" wire:ignore>
                                <canvas id="admin-view-count-chart"  style="width:100%;max-width:800px" ></canvas>
                            </div>

                            <div class="col-6" wire:ignore>
                                <canvas id="admin-subscribers-chart"  style="width:100%;max-width:800px" ></canvas>
                            </div>


                            <div class="col-6 mt-2" wire:ignore>
                                <canvas id="admin-premium-users-chart"  style="width:100%;max-width:800px" ></canvas>
                            </div>

                            <div class="col-6 mt-2" wire:ignore>
                                <canvas id="admin-benefit-chart"  style="width:100%;max-width:100%; max-height: 400px" ></canvas>
                            </div>

                            <div class="col-12 mt-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2"
                                    id="admin-extra-charts-toggle"
                                    data-admin-extra-chart-toggle
                                    data-expanded="false"
                                    aria-expanded="false"
                                    aria-label="Qo'shimcha chartlarni ko'rsatish"
                                    style="height: 28px; line-height: 1;"
                                >
                                    <span data-toggle-icon-right>&gt;</span>
                                    <span class="d-none" data-toggle-icon-down>&#709;</span>
                                </button>
                            </div>

                            <div class="col-6 mt-2 d-none admin-extra-chart" wire:ignore style="min-height: 320px;">
                                <canvas id="admin-credit-users-chart"  style="width:100%;max-width:800px; height:320px;" ></canvas>
                            </div>
                            <div class="col-6 mt-2 d-none admin-extra-chart" wire:ignore style="min-height: 320px;">
                                <canvas id="admin-benefit-credit-chart"   style="width:100%;max-width:100%; height:320px;" ></canvas>
                            </div>
                            <div class="col-6 mt-2 d-none admin-extra-chart" wire:ignore style="min-height: 320px;">
                                <canvas id="admin-paid-referral-chart"   style="width:100%;max-width:100%; height:320px;" ></canvas>
                            </div>
                            <div class="col-6 mt-2 d-none admin-extra-chart" wire:ignore style="min-height: 320px;">
                                <canvas id="admin-referral-chart"   style="width:100%;max-width:100%; height:320px;" ></canvas>
                            </div>


                        </div>



                        <div class="row pt-5">
                            <div class="col-6">
                                <h1>Eng ko'p ko'rilgan videolar (TO'P 10)</h1>
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">№</th>
                                        <th scope="col">Video linki</th>
                                        <th scope="col">Ko'rilgan soni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $counter=1;
                                    @endphp
                                    @foreach($mostViewedVideo as $video)
                                        @if($video->watcheds_count > 0)
                                            <tr>
                                                <th scope="row">{{$counter++}}</th>
                                                <td><a target="_blank" style="text-decoration: none;" href="{{$video->link}}">Videoga o'tish</a></td>
                                                <td>{{$video->view_count}}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <div class="col-6">
                                <h1>Eng ko'p saqlangan videolar (TO'P 10)</h1>
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">№</th>
                                        <th scope="col">Video linki</th>
                                        <th scope="col">Saqlanganlar soni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $counter=1;
                                    @endphp
                                    @foreach($mostViewedSave as $video)
                                        @if($video->saveds_lessons_count > 0)
                                            <tr>
                                                <th scope="row">{{$counter++}}</th>
                                                <td><a target="_blank" style="text-decoration: none;" href="{{$video->link}}">Videoga o'tish</a></td>
                                                <td>{{$video->saveds_lessons_count}}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page content -->
        </div>
        <footer class="footer">
            <div class="row align-items-center justify-content-xl-between">
                <div class="col-xl-6 m-auto text-center">
                    <div class="copyright">

                    </div>
                </div>
            </div>
        </footer>
        </body>
    </div>
</div>


@script
<script>
    let videoViewChart, subscribersChart, benefitChart,benefitCreditChart, premiumUsersChart, creditUsersChart,paidReferralChart,referralChart;

    function refreshAdminExtraCharts() {
        [
            creditUsersChart,
            benefitCreditChart,
            paidReferralChart,
            referralChart,
        ].forEach(function (chart) {
            if (chart) {
                chart.resize();
                chart.update('none');
            }
        });
    }

    if (!window.adminExtraChartsToggleInitialized) {
        document.addEventListener('click', function (event) {
            const toggleButton = event.target.closest('[data-admin-extra-chart-toggle]');

            if (!toggleButton) {
                return;
            }

            const isExpanded = toggleButton.getAttribute('data-expanded') === 'true';
            const nextExpandedState = !isExpanded;
            const extraCharts = document.querySelectorAll('.admin-extra-chart');
            const rightIcon = toggleButton.querySelector('[data-toggle-icon-right]');
            const downIcon = toggleButton.querySelector('[data-toggle-icon-down]');

            extraCharts.forEach(function (chartWrapper) {
                chartWrapper.classList.toggle('d-none', !nextExpandedState);
            });

            toggleButton.setAttribute('data-expanded', nextExpandedState ? 'true' : 'false');
            toggleButton.setAttribute('aria-expanded', nextExpandedState ? 'true' : 'false');

            if (rightIcon) {
                rightIcon.classList.toggle('d-none', nextExpandedState);
            }

            if (downIcon) {
                downIcon.classList.toggle('d-none', !nextExpandedState);
            }

            if (nextExpandedState) {
                requestAnimationFrame(function () {
                    refreshAdminExtraCharts();
                    setTimeout(refreshAdminExtraCharts, 150);
                });
            }
        });

        window.adminExtraChartsToggleInitialized = true;
    }

    document.addEventListener('ReloadVideoViewChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const view_count_chart_element = document.getElementById('admin-view-count-chart');

        if (videoViewChart) {
            videoViewChart.destroy();
        }

        videoViewChart = new Chart(view_count_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Ko`rishlar soni ',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });

    document.addEventListener('SubscribersViewChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const subscribers_chart_element = document.getElementById('admin-subscribers-chart');

        if (subscribersChart) {
            subscribersChart.destroy();
        }

        subscribersChart = new Chart(subscribers_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Obunachilar soni ',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });

    document.addEventListener('BenefitPriceChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const benefit_chart_element = document.getElementById('admin-benefit-chart');

        if (benefitChart) {
            benefitChart.destroy();
        }

        benefitChart = new Chart(benefit_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Premium Daromat',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });

    document.addEventListener('BenefitCreditPriceChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const benefit_chart_element = document.getElementById('admin-benefit-credit-chart');

        if (benefitCreditChart) {
            benefitCreditChart.destroy();
        }

        benefitCreditChart = new Chart(benefit_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Credit Daromat',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
            options: {
            responsive: true,
                maintainAspectRatio: false,
        }
        });
    });

    document.addEventListener('PaidReferralChartData', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const paid_referral_chart_element = document.getElementById('admin-paid-referral-chart');

        if (paidReferralChart) {
            paidReferralChart.destroy();
        }

        paidReferralChart = new Chart(paid_referral_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Tolangan Referallar',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
            options: {
            responsive: true,
                maintainAspectRatio: false,
        }
        });
    });

    document.addEventListener('ReferralChartData', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartReferralData = event.detail['0']['1'];

        const referral_chart_element = document.getElementById('admin-referral-chart');

        if (referralChart) {
            referralChart.destroy();
        }

        referralChart = new Chart(referral_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Kunlik Tolanmagan Referallar',
                    data: chartReferralData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
            options: {
            responsive: true,
                maintainAspectRatio: false,
        }
        });
    });

    document.addEventListener('PremiumUsersChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const premium_users_chart_element = document.getElementById('admin-premium-users-chart');

        if (premiumUsersChart) {
            premiumUsersChart.destroy();
        }

        premiumUsersChart = new Chart(premium_users_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Premium Sotib Olgan Foydalanuvchilar',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });

    document.addEventListener('CreditUsersChart', function (event) {
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];

        const premium_users_chart_element = document.getElementById('admin-credit-users-chart');

        if (creditUsersChart) {
            creditUsersChart.destroy();
        }

        creditUsersChart = new Chart(premium_users_chart_element, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Credit Sotib Olgan Foydalanuvchilar',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });
</script>

@endscript
