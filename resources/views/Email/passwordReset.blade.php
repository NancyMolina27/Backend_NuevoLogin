@component('mail::message')
# Cambia tu contraseña

Da click en el botón para cambiar tú contraseña. 

@component('mail::button', ['url' => 'http://localhost:4200/cambiar-contraseña?token='.$token])
Cambiar contraseña
@endcomponent

Gracias<br>
{{ config('') }}
@endcomponent
