@component('mail::message')
# 🔐 Permintaan Reset Password

Ada permintaan reset password baru dengan detail berikut:

---

**Login:** {{ $ticket->login }}

**User terdaftar:**  @if($ticket->user_id) Ya (User ID: {{ $ticket->user_id }})
@else Tidak ditemukan di sistem
@endif

**Catatan dari pengguna:**  
{{ $ticket->note ?? '-' }}

**Status:** {{ ($ticket->status) }}

**Waktu Request:** {{ $ticket->created_at->format('d M Y H:i') }}

---

@component('mail::button', ['url' => route('password_reset_requests.index')])
Buka Daftar Request Reset Password
@endcomponent

Silakan segera ditindaklanjuti oleh Superadmin.

Terima kasih,  
**Aplikasi Bintang**
@endcomponent