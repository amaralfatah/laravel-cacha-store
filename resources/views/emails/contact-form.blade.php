@component('mail::message')
    # New Contact Form Submission

    You have received a new message from the contact form on {{ $store->name }} website.

    ## Contact Details:
    **Name:** {{ $name }}
    **Email:** {{ $email }}

    ## Message:
    {{ $message }}

    @component('mail::button', ['url' => config('app.url')])
        Go to Website
    @endcomponent

    Thank you,<br>
    {{ config('app.name') }}
@endcomponent
