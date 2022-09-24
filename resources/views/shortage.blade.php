@component('mail::message')
This is to inform you that {{$handler}} of {{$branch}} has posted a shortage of {{$short}}.
on {{now()}} against the company policy.
Therefore (handler)’s account is hereby suspended till reconciliation.
@endcomponent