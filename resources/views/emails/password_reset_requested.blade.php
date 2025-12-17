<p>Ada permintaan reset password.</p>
<p><b>Login:</b> {{ $ticket->login }}</p>
<p><b>User ID:</b> {{ $ticket->user_id ?? '-' }}</p>
<p><b>Catatan:</b> {{ $ticket->note ?? '-' }}</p>
<p>Status: {{ $ticket->status }}</p>