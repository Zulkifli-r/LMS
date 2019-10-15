<h1>Selamat bergabung</h1>

@if (!$user->email_verified_at && !$user->is_provider)
   <p>{{ \URL::temporarySignedRoute('fe-verify-route', \Carbon\Carbon::now()->addMinutes(60), [ 'id'=> $user->id, 'hash' => sha1($user->email)]) }}</p>
@endif