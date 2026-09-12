<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100">

    <div class="flex h-screen overflow-hidden">

        <livewire:sidebar />

        <div class="flex flex-col flex-1 overflow-hidden">
            <livewire:navbar :title="$title" />

            <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
