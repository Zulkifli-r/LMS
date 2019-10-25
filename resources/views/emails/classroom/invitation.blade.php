You have been invited to join
{{ $invitation->classroom->name }}
as {{ $invitation->invited_as }}

please follow the link below to accept your invitation
{{ \URL::temporarySignedRoute('fe-invitation-route', now()->addDays(1),[
                'classroom' => $invitation->classroom->slug,
                'token' => $token
            ]) }}
