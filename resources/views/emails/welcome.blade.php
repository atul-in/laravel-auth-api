@component('mail::message')
Welcome to Todo-App

Name: {{$mailData['name']}} <br/>
Email: {{$mailData['email']}} <br/>

Thanks, <br/>
{{config('app.name')}}
@endcomponent