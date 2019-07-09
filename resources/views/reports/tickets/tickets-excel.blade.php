<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>MSC USER</th>
        <th>BL NUMBER</th>
        <th>PAYMENT TYPE</th>
        <th>CUSTOMER</th>
        <th>RECEIVED TIME</th>
        <th>FINAL TRANSACTION</th>
        <th>MINUTES</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($tickets))
        @foreach($tickets as $k => $ticket)
            <tr>
                <td>{{ $ticket->id  }} </td>
                <td>{{ $ticket->user_assigned  }}</td>
                <td>{{ $ticket->bl_number }}</td>
                <td>{{ $ticket->payment_type_description  }}</td>
                <td>{{ $ticket->customer_name  }}</td>
                <td>{{ $ticket->ticket_created_format  }}</td>
                <td>{{ $ticket->completed_date_format  }}</td>
                <td>{{ $ticket->minutes_total  }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>