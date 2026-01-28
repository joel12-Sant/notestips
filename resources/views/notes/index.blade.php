@extends('layout.notes.app')

@section('title', 'Mis notas | NotesTips')

@section('main-content')
    <div class="h-full flex overflow-hidden">
        <aside class="w-80 shrink-0 border-r border-slate-200 bg-white overflow-auto">
            @include('partials.notes.list')
        </aside>

        <section class="flex-1 overflow-auto bg-slate-50">
            <div class="max-w-4xl mx-auto p-8">
                @isset($note)
                    {{-- @include('partials.notes.show', ['note' => $note]) --}}
                @else
                    <div class="h-full flex items-center justify-center text-slate-500">
                        Selecciona una nota para ver su contenido
                    </div>
                @endisset
            </div>
        </section>
    </div>
@endsection
