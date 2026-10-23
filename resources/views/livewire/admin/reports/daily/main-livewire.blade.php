<div class="">
    @php
        $isMonthly = (int) $type === 1;
        $periodTitle = $isMonthly ? 'Oylik' : 'Kunlik';
        $periodViewTitle = $isMonthly ? 'Bir oylik foydalanish ko‘rinishi' : 'Bir kunlik foydalanish ko‘rinishi';
        $periodButton = $isMonthly ? 'Bu oy' : 'Bugun';
        $periodUsageText = $isMonthly ? 'Oy davomida' : 'Kun davomida';
        $periodCurrentText = $isMonthly ? 'shu oyda' : 'bugungi kunda';
    @endphp
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $periodTitle }} hisobotlar</span>
                    <span class="badge badge-light">Demo dizayn</span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <h3 class="report-hero-title">{{ $periodViewTitle }}</h3>
                        </div>
                        <div class="col-md-5">
                            <div class="report-toolbar-inline">
                                <button class="btn btn-primary btn-sm" type="button" wire:click="setToday">{{ $periodButton }}</button>
                                <div class="w-40">
                                    <select wire:model.live="selectedYear" class="form-control">
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-40">
                                    <select wire:model.live="selectedMonth" class="form-control">
                                        @foreach($availableMonths as $monthValue => $monthLabel)
                                            <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(!$isMonthly)
                                    <div class="w-40">
                                        <select wire:model.live="selectedDay" class="form-control">
                                            @foreach($availableDays as $day)
                                                <option value="{{ $day }}">{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4 mb-3">
            <div class="card report-stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">{{ $periodTitle }} faol foydalanuvchilar</div>
                    <div class="stat-value">{{$activeUsersCount}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card report-stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">O‘rtacha sessiya soni</div>
                    <div class="stat-value">{{ $averageSessionCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card report-stat-card h-100">
                <div class="card-body">
                    <div class="stat-label">Eng faol vaqt oralig‘i</div>
                    <div class="stat-value">{{$activeTime}}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    Yechilgan mashqlar soni
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="report-exercise-card">
                                <div class="card-body">
                                    <div class="report-exercise-label">
                                        <span>Reading</span>
                                        <span class="badge badge-primary">Oddiy</span>
                                    </div>
                                    <div class="report-exercise-count">{{ $examCounts['reading'] }}</div>
                                    <div class="report-exercise-text">{{ $periodUsageText }} yakunlangan reading urinishlari</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="report-exercise-card">
                                <div class="card-body">
                                    <div class="report-exercise-label">
                                        <span>Listening</span>
                                        <span class="badge badge-success">Oddiy</span>
                                    </div>
                                    <div class="report-exercise-count">{{ $examCounts['listening'] }}</div>
                                    <div class="report-exercise-text">{{ $periodUsageText }} yakunlangan listening urinishlari</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="report-exercise-card">
                                <div class="card-body">
                                    <div class="report-exercise-label">
                                        <span>Writing</span>
                                        <span class="badge badge-warning">AI / Oddiy</span>
                                    </div>
                                    <div class="report-exercise-count">{{ $examCounts['writing'] }}</div>
                                    <div class="report-exercise-text">{{ $periodTitle }} writing urinishlari soni</div>
                                    <div class="report-exercise-split">
                                        <div class="report-exercise-split-item">
                                            <div class="report-exercise-split-label">AI bilan</div>
                                            <div class="report-exercise-split-value">{{ $examCounts['ai_writing'] }} ta</div>
                                        </div>
                                        <div class="report-exercise-split-item">
                                            <div class="report-exercise-split-label">Oddiy</div>
                                            <div class="report-exercise-split-value">{{ $examCounts['plain_writing'] }} ta</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="report-exercise-card">
                                <div class="card-body">
                                    <div class="report-exercise-label">
                                        <span>Speaking</span>
                                        <span class="badge badge-info">AI / Oddiy</span>
                                    </div>
                                    <div class="report-exercise-count">{{ $examCounts['speaking'] }}</div>
                                    <div class="report-exercise-text">{{ $periodTitle }} speaking urinishlari soni</div>
                                    <div class="report-exercise-split">
                                        <div class="report-exercise-split-item">
                                            <div class="report-exercise-split-label">AI bilan</div>
                                            <div class="report-exercise-split-value">{{ $examCounts['ai_speaking'] }} ta</div>
                                        </div>
                                        <div class="report-exercise-split-item">
                                            <div class="report-exercise-split-label">Oddiy</div>
                                            <div class="report-exercise-split-value">{{ $examCounts['plain_speaking'] }} ta</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="report-exercise-card">
                                <div class="card-body">
                                    <div class="report-exercise-label">
                                        <span>Full Mock</span>
                                        <span class="badge badge-secondary">Umumiy</span>
                                    </div>
                                    <div class="report-exercise-count">{{ $examCounts['full'] }}</div>
                                    <div class="report-exercise-text">{{ $periodUsageText }} yakunlangan full mock urinishlari</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    Qisqa jamlama
                </div>
                <div class="card-body">
                    <div class="report-exercise-summary">
                        <div class="h6 mb-2">Jami yechilganlar</div>
                        <div class="h3 mb-0">{{ $examCounts['reading'] + $examCounts['listening'] + $examCounts['writing'] + $examCounts['speaking'] + $examCounts['full'] }} ta</div>
                    </div>

                    <div class="report-exercise-summary">
                        <div class="h6 mb-2">Eng ko‘p yechilgan</div>
                        <div class="mb-0">
                            {{ collect([
                                'Reading' => $examCounts['reading'],
                                'Listening' => $examCounts['listening'],
                                'Writing' => $examCounts['writing'],
                                'Speaking' => $examCounts['speaking'],
                                'Full mock' => $examCounts['full'],
                            ])->sortDesc()->keys()->first() ?? '-' }}
                            {{ $periodCurrentText }} asosiy oqimni olib kelgan.
                        </div>
                    </div>

                    <div class="report-exercise-summary">
                        <div class="h6 mb-2">AI ishlatilgan yo‘nalish</div>
                        <div class="mb-0">Writing: {{ $examCounts['ai_writing'] }} ta, Speaking: {{ $examCounts['ai_speaking'] }} ta.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--    <div class="row">--}}
{{--        <div class="col-12">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">--}}
{{--                    Kunlik foydalanish jadvali--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="table-responsive">--}}
{{--                        <table class="table table-bordered table-striped report-table mb-0">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>Bo‘lim</th>--}}
{{--                                <th>Foydalanishlar soni</th>--}}
{{--                                <th>Ulushi</th>--}}
{{--                                <th>Holat</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            <tr>--}}
{{--                                <td>Reading</td>--}}
{{--                                <td>472</td>--}}
{{--                                <td>38%</td>--}}
{{--                                <td><span class="badge badge-primary">Yuqori</span></td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>Listening</td>--}}
{{--                                <td>336</td>--}}
{{--                                <td>27%</td>--}}
{{--                                <td><span class="badge badge-success">Barqaror</span></td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>Writing</td>--}}
{{--                                <td>236</td>--}}
{{--                                <td>19%</td>--}}
{{--                                <td><span class="badge badge-warning">O‘rta</span></td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>Speaking</td>--}}
{{--                                <td>137</td>--}}
{{--                                <td>11%</td>--}}
{{--                                <td><span class="badge badge-info">Kuzatuvda</span></td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>Premium / Boshqa</td>--}}
{{--                                <td>62</td>--}}
{{--                                <td>5%</td>--}}
{{--                                <td><span class="badge badge-secondary">Past</span></td>--}}
{{--                            </tr>--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
