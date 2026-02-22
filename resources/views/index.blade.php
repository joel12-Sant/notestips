@extends('layout.app')

@section('title', 'Inicio | NotesTips')

@section('main-content')
    <main class="min-h-screen bg-(--color-bg) text-(--color-text)">
        <header class="border-b border-(--color-border) bg-(--color-surface)">
            <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                    <svg class="w-7 h-7 text-(--color-primary)" viewBox="0 0 24 24" fill="none" aria-hidden="true"
                        focusable="false">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor"
                            stroke-width="2" stroke-linejoin="round" />
                        <path d="M8 6h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M8 10h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span>NotesTips</span>
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('notes.index') }}"
                            class="px-4 py-2 rounded-lg text-white bg-(--color-primary) hover:brightness-95">
                            Ir a mis notas
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 rounded-lg border border-(--color-border) bg-(--color-surface) hover:bg-slate-100">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 rounded-lg text-white bg-(--color-primary) hover:brightness-95">
                            Crear cuenta
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <section class="mx-auto max-w-6xl px-4 py-16">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <p
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-(--color-primary-soft) text-(--color-primary)">
                        Organiza tus ideas sin fricción
                    </p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-semibold leading-tight">
                        Tus notas, tareas y recordatorios en un solo lugar
                    </h1>
                    <p class="mt-5 text-lg text-(--color-text-muted)">
                        NotesTips te permite crear notas rápidas, escribir en Markdown y gestionar tareas con checklists
                        interactivas.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('notes.index') }}"
                                class="px-5 py-3 rounded-xl text-white bg-(--color-primary) hover:brightness-95">
                                Abrir panel de notas
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-5 py-3 rounded-xl text-white bg-(--color-primary) hover:brightness-95">
                                Empezar gratis
                            </a>
                            <a href="{{ route('login') }}"
                                class="px-5 py-3 rounded-xl border border-(--color-border) bg-(--color-surface) hover:bg-slate-100">
                                Ya tengo cuenta
                            </a>
                        @endauth
                    </div>
                </div>

                <article class="rounded-2xl border border-(--color-border) bg-(--color-surface) p-4 shadow-sm">
                    <h2 class="px-2 pt-2 text-lg font-semibold">Vista real de la aplicación</h2>
                    <img src="{{ asset('images/notes-dashboard-real.png') }}"
                        alt="Vista previa del panel de NotesTips"
                        onerror="this.onerror=null;this.src='{{ asset('images/notes-dashboard-preview.svg') }}';"
                        class="mt-3 w-full rounded-xl border border-(--color-border) object-contain bg-slate-50"
                        loading="lazy">
                </article>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 pb-16">
            <div class="grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-(--color-border) bg-(--color-surface) p-5">
                    <h3 class="font-semibold">Editor simple</h3>
                    <p class="mt-2 text-sm text-(--color-text-muted)">
                        Crea y edita notas de forma directa con una interfaz limpia y rápida.
                    </p>
                </article>

                <article class="rounded-2xl border border-(--color-border) bg-(--color-surface) p-5">
                    <h3 class="font-semibold">Vista Markdown</h3>
                    <p class="mt-2 text-sm text-(--color-text-muted)">
                        Al abrir una nota, se renderiza con formato para mejorar la lectura del contenido.
                    </p>
                </article>

                <article class="rounded-2xl border border-(--color-border) bg-(--color-surface) p-5">
                    <h3 class="font-semibold">Checklist interactiva</h3>
                    <p class="mt-2 text-sm text-(--color-text-muted)">
                        Marca tareas completadas directamente en la vista de la nota.
                    </p>
                </article>
            </div>
        </section>
    </main>
@endsection
