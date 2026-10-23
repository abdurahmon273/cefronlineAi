<table>
    <thead>
    <tr>
        <th>count</th>
        <th>ID</th>
        <th>F.I.O</th>
        <th>Telefon</th>
        <th>Start</th>
        <th>End</th>
        <th>Plan</th>
        <th>Type</th>
        <th>Price</th>
        <th>Promocode</th>
        <th>Discount</th>
        <th>Summa</th>
        <th>Summa Discounted</th>
    </tr>
    </thead>
    <tbody>
    @foreach($premium_users as $key=>$premium_to_user)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $premium_to_user->id }}</td>
            <td>{{ $premium_to_user->user->name ?? '' }} {{ $premium_to_user->user->surname ?? '' }}</td>
            <td>{{ $premium_to_user->user->mobil_number ?? '' }}</td>
            <td>{{ $premium_to_user->start_time }}</td>
            <td>{{ $premium_to_user->end_time }}</td>
            <td>{{ $premium_to_user->plan->name ?? '' }}</td>
            <td>{{ $premium_to_user->type=='0'  ? 'App' : ( $premium_to_user->type=='2' ? 'Web' : ( $premium_to_user->admin ? "By ".$premium_to_user->admin->name : 'By Admin') )}}</td>
            <td>{{ number_format($premium_to_user->price,2,'.',' ') }}</td>
            <td>{{ $premium_to_user->promoCode ? $premium_to_user->promoCode->code : '' }}</td>
            <td>{{ $premium_to_user->promoCode ? $premium_to_user->promoCode->discount.'%' : '' }} </td>
            <td>{{intval($premium_to_user->price)}}</td>
            <td>{{ $premium_to_user->promoCode ? (intval($premium_to_user->price)/100*(100-intval($premium_to_user->promoCode->discount))) : $premium_to_user->price}}</td>
        </tr>

    @endforeach
    <tr>
        <td colspan="8"></td>
        <td>{{ number_format(($premium_users->sum('price')),'0','.',' ') }}</td>
        <td></td>
        <td></td>
        <td>{{intval($premium_users->sum('price'))}}</td>
    </tr>
    </tbody>
</table>
