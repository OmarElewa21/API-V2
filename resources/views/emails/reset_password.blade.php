@component('mail::message')
# Hello

[This is your Password Reset Link]({{$link}})


Thanks,<br>
{{ config('app.name') }}
@endcomponent