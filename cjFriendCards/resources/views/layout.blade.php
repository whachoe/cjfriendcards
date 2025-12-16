<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'cjFriendCards')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/htmx.org"></script>
</head>
<body class="bg-primary-light">
    <nav class="bg-primary-dark shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-1">
                    <img src="{{ asset('logo.svg') }}" alt="cjFriendCards Logo" width="60" height="60">
                    <a href="{{ route('cards.index') }}" class="text-2xl font-bold text-primary-light">cjFriendCards</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cards.index') }}" class="text-primary-light hover:text-primary-accent">Cards</a>
                    <a href="{{ route('cards.birthday-calendar') }}" class="text-primary-light hover:text-primary-accent">Birthdays</a>
                    <a href="{{ route('cards.create') }}" class="bg-primary-accent text-white px-4 py-2 rounded hover:bg-primary-danger">New Card</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-primary-secondary border border-primary-accent text-primary-dark rounded">
                {{ $message }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
