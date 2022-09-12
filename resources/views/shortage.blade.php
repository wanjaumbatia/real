@component('mail::message')
    An account belonging to {{$handler}} who is a sales executive from {{$branch}} has just been reconciled by {{$user}} with a shortage of {{$short}}.
    Expected amount was {{$expected}} on {{now()}}
@component('mail::button', ['url'=>'www.google.com'])
    View Report
@endcomponent

@endcomponent