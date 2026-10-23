<?php

namespace App\Exports;

use App\Models\Promocode;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PromoCodeExport implements FromView
{
    protected $search;
    protected $type;
    protected $start_date;
    protected $end_date;
    protected $plan_ids = [];

    /**
     * @param array|int|string|null $plan_ids  Bir yoki bir nechta plan ID(lar)i
     */
    public function __construct($search, $type, $start_date, $end_date, $plan_ids = [])
    {
        $this->search     = $search;
        $this->type       = $type;
        $this->start_date = $start_date;
        $this->end_date   = $end_date;

        // plan_ids'ni har doim massivga va int'ga keltiramiz
        $plan_ids = is_array($plan_ids) ? $plan_ids : (array) $plan_ids;
        $this->plan_ids = array_values(array_filter(array_map('intval', $plan_ids), fn($v) => $v > 0));
    }

    public function view(): View
    {
        $promocodes = Promocode::with(['plans:id,name'])
            ->when($this->search, fn($q) =>
            $q->where('code', 'like', "%{$this->search}%")
            )
            ->when($this->start_date, fn($q) =>
            $q->whereDate('end_date', '>=', $this->start_date)
            )
            ->when($this->end_date, fn($q) =>
            $q->whereDate('end_date', '<=', $this->end_date)
            )
            // type=0 bo‘lsa ham ishlashi uchun:
            ->when($this->type !== null && $this->type !== '', fn($q) =>
            $q->where('type', $this->type)
            )
            // bir nechta plan bo‘lsa kamida bittasiga tegishli bo‘lgan promolarni chiqarish
            ->when(!empty($this->plan_ids), fn($q) =>
            $q->whereHas('plans', fn($pq) =>
            $pq->whereIn('plans.id', $this->plan_ids)
            )
            )
            ->orderByDesc('id')
            ->get();

        return view('exports.promocodes', compact('promocodes'));
    }
}
