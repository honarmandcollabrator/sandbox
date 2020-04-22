@component('mail::message')
    لینک بازیابی رمز عبور
    @component('mail::button', ['url' => $resetUrlWithToken, 'color' => 'success'])
        لینک
    @endcomponent

    با تشکر،
    {{ config('app.name') }}
@endcomponent

