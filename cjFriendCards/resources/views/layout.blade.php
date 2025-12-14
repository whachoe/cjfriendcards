<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'cjFriendCards')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('cards.index') }}" class="text-2xl font-bold text-blue-600">cjFriendCards</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cards.index') }}" class="text-gray-600 hover:text-gray-900">Cards</a>
                    <a href="{{ route('cards.birthday-calendar') }}" class="text-gray-600 hover:text-gray-900">Birthdays</a>
                    <a href="{{ route('cards.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">New Card</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
