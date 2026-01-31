<aside class="h-full w-72 border- bg-white">
    <div class="flex h-full flex-col px-4 py-5">
        <header>
            <a href="{{ route('notes.index') }}" class="flex items-center gap-2">
                <svg class="w-8 h-8 text-(--color-primary)" viewBox="0 0 24 24" fill="none" aria-hidden="true"
                    focusable="false">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" />
                    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor"
                        stroke-width="2" stroke-linejoin="round" />
                    <path d="M8 6h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M8 10h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>

                <h2 class="text-lg font-semibold text-slate-900">
                    NotesTips
                </h2>
            </a>
        </header>

        <hr class="my-4 border-slate-200">

        <nav aria-label="Acciones de NotesTips" class="space-y-3">
            <form action="{{ route('notes.create') }}" method="GET">
                <button type="submit"
                    class="cursor-pointer inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white
                           shadow-sm hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <span aria-hidden="true">+</span>
                    Crear Nota
                </button>
            </form>
        </nav>

        <div class="mt-auto pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="cursor-pointer inline-flex w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700
                           hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</aside>
