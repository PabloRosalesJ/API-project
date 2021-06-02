@component('mail::message')
# Hola {{$user->name}}

has cambiado tu correo email. Por favor verifica tu nueva dirección.

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verificar
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
