<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Type</th>
        <th>Start date</th>
        <th>End date</th>
        <th>Discount (%)</th>
        <th>Plans</th>
        <th>Status</th>
        <th>Created at</th>
    </tr>
    </thead>
    <tbody>
    @foreach($promocodes as $promo)
        @php
            // Tur nomini modeldagi TYPES dan olamiz (agar bo‘lmasa, raqamni ko‘rsatamiz)
            $typeLabel = \App\Models\Promocode::TYPES[$promo->type] ?? $promo->type;

            // Sana formatlari
            $sd = $promo->start_date instanceof \Carbon\Carbon
                    ? $promo->start_date->format('Y-m-d')
                    : (is_string($promo->start_date) ? substr($promo->start_date, 0, 10) : '');

            $ed = $promo->end_date instanceof \Carbon\Carbon
                    ? $promo->end_date->format('Y-m-d')
                    : (is_string($promo->end_date) ? substr($promo->end_date, 0, 10) : '');

            $created = $promo->created_at ? \Carbon\Carbon::parse($promo->created_at)->format('Y-m-d H:i') : '';

            // Plan nomlari
            $planNames = $promo->plans && $promo->plans->count()
                ? $promo->plans->pluck('name')->join(', ')
                : '';

            // Status label (sizdagi ro'yxatga mos)
            $statusLabel = ($promo->status == '0') ? 'Generated' : 'By Admin';
        @endphp
        <tr>
            <td>{{ $promo->id }}</td>
            <td>{{ $promo->code }}</td>
            <td>{{ $typeLabel }}</td>
            <td>{{ $sd }}</td>
            <td>{{ $ed }}</td>
            <td>{{ $promo->discount }}%</td>
            <td>{{ $planNames }}</td>
            <td>{{ $statusLabel }}</td>
            <td>{{ $created }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
