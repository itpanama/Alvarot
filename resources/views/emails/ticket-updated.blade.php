Hi {{ $ticket['customer_name'] }},<br><br>
{{ $ticket['comments'] }}<br>

@if (!empty($ticket['user_assigned']))
    <br>
    User assigned: <strong>{{ $ticket['user_assigned'] }}</strong>.
@endif
