<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'cjFriendCards')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/htmx.org"></script>
</head>
<body class="bg-[#fef3c7]">
    <nav class="bg-[#8b4513] shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('cards.index') }}" class="text-2xl font-bold text-[#fef3c7]">cjFriendCards</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cards.index') }}" class="text-[#fef3c7] hover:text-[#ff6b35]">Cards</a>
                    <a href="{{ route('cards.birthday-calendar') }}" class="text-[#fef3c7] hover:text-[#ff6b35]">Birthdays</a>
                    <a href="{{ route('cards.create') }}" class="bg-[#ff6b35] text-white px-4 py-2 rounded hover:bg-[#d7263d]">New Card</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-[#f5c518] border border-[#ff6b35] text-[#8b4513] rounded">
                {{ $message }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
