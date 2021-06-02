@component('mail::message')
# Hola {{$user->name}}

Gracias por registrarte, por favor verifica tu cuenta.

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verificar
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
