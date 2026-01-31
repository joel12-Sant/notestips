<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', 'NotesTips')</title>
</head>

<body class="bg-slate-50">

    <div x-data="{ sidebarOpen: true }" class="h-screen flex overflow-hidden">
        <div class="shrink-0 bg-white border-r border-slate-200 overflow-hidden transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-72' : 'w-0'">
            @include('partials.notes.sidebar')
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-14 bg-white border-b border-slate-200 flex items-center px-4 gap-3">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                    aria-label="Abrir/cerrar sidebar">
                    <span class="text-lg" x-text="sidebarOpen ? '✕' : '☰'"></span>
                </button>

                <form action="{{ route('notes.index') }}" method="GET"
                    class="flex items-center border pl-4 gap-2 border-gray-500/40 h-11.5 rounded-2xl overflow-hidden w-full
                         focus-within:ring-blue-600 focus-within:ring-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 30 30"
                        fill="#6B7280">
                        <path
                            d="M13 3C7.489 3 3 7.489 3 13s4.489 10 10 10a9.95 9.95 0 0 0 6.322-2.264l5.971 5.971a1 1 0 1 0 1.414-1.414l-5.97-5.97A9.95 9.95 0 0 0 23 13c0-5.511-4.489-10-10-10m0 2c4.43 0 8 3.57 8 8s-3.57 8-8 8-8-3.57-8-8 3.57-8 8-8" />
                    </svg>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" autocomplete="off"
                        placeholder="Search"
                        class="w-full h-full outline-none text-gray-500 bg-transparent placeholder-gray-500 text-sm">
                </form>
            </header>

            <main class="flex-1 overflow-hidden">
                @yield('main-content')
            </main>
        </div>
    </div>
    @stack('scripts')
    @vite('resources/js/pages/list.js')
</body>

</html>
