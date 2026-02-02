<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', 'NotesTips')</title>
</head>

<body class="bg-slate-50" data-page="@yield('page')">

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
            <div id="filterChips" class="px-4 py-2 bg-white border-b border-slate-200 hidden">

            </div>

            <div class="mt-2 bg-white border-b border-slate-200">
                <div class="px-4 py-3">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="importance" class="block text-xs font-medium text-slate-600 mb-1">
                                Importancia
                            </label>
                            <select id="importance" name="importance"
                                class="w-full h-10 px-3 rounded-xl border bg-slate-50 text-slate-900 border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                <option value="">-- Todos --</option>
                                <option value="none">Sin importancia</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Fecha
                            </label>
                            <div class="flex gap-2">
                                <select id="due_date_mode" name="due_date_mode"
                                    class="h-10 px-3 rounded-xl border bg-slate-50 text-slate-900 border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                    <option value="">Todas</option>
                                    <option value="with">Con fecha</option>
                                    <option value="none">Sin fecha</option>
                                    <option value="exact">Fecha exacta</option>
                                </select>

                                <input id="due_date" type="date" name="due_date" value="{{ request('due_date') }}"
                                    class="w-full h-10 px-3 rounded-xl border bg-slate-50 text-slate-900 border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                            </div>
                        </div>
                        <div>
                            <label for="order_by" class="block text-xs font-medium text-slate-600 mb-1">Ordenar
                                Por</label>
                            <select name="order_by" id="order_by"
                                class="h-10 px-3 rounded-xl border bg-slate-50 text-slate-900 border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                <option value="">-- Por defecto --</option>
                                <option value="created_at">Fecha de creación</option>
                                <option value="due_date">Fecha de realización</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 overflow-hidden">
                @yield('main-content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
