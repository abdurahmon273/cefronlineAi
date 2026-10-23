<table>
    <thead>
    <tr>
        <th>No</th>
        <th>ID</th>
        <th>Full Name</th>
        <th>Tel Number</th>
        <th>Active Price</th>
        <th>Paid Price</th>
        <th>Total Price</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $key => $user)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $user->id }}</td>
            <td>{{ trim(($user->name ?? '') . ' ' . ($user->surname ?? '')) }}</td>
            <td style="mso-number-format:'\@';">{{ $user->mobil_number ?? '' }}</td>
            <td>{{ $user->active_price }}</td>
            <td>{{ $user->paid_price }}</td>
            <td>{{ $user->total_price }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
