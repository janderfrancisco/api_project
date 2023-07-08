@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => 'http://localhost/change-password?token='.$token])
Alterar Senha
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
