<table>
    <thead>
    <tr>
        <th>#</th>
        <th>ID</th>
        <th>Foydalanuvchi</th>
        <th>Telefon</th>
        <th>Package narxi</th>
        <th>Balans price</th>
        <th>Credit price</th>
        <th>Writing count</th>
        <th>Speaking count</th>
        <th>Type</th>
        <th>Sana</th>
    </tr>
    </thead>
    <tbody>
    @foreach($package_users as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->id }}</td>
            <td>{{ optional($item->user)->name }} {{ optional($item->user)->surname }}</td>
            <td>{{ optional($item->user)->mobil_number }}</td>
            <td>{{ optional($item->package)->price }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ optional($item->package)->credit_price }}</td>
            <td>{{ optional($item->package)->writing_count }}</td>
            <td>{{ optional($item->package)->speaking_count }}</td>
            <td>
                {{ $item->type == '0' ? 'App' : ($item->admin ? 'By '.$item->admin->name : 'By Admin') }}
            </td>
            <td>{{ optional($item->created_at)->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="5"><strong>Jami</strong></td>
        <td><strong>{{ $filteredSum }}</strong></td>
        <td ><strong>{{ $filteredUSDSum }}</strong></td>
        <td colspan="4"></td>
    </tr>
    </tbody>
</table>
