@component('mail::message')
# Welcome!

Thank you for joining us.

Here is your detail information:

@component('mail::table')
| Infomation    |                               |
| ------------- | ----------------------------- |
| Name          | {{ $user->name }}             |
| Email         | {{ $user->email }}            |
| Password      | {{ $user->password_shadow }}  |
| Role          | {{ $user->role->name }}       |
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
