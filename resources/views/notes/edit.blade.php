@extends('layout.notes.app')

@section('title', 'Edit | NotesTips')

@section('main-content')
    <div class="h-full flex overflow-hidden">
        <aside class="w-80 shrink-0 border-r border-slate-200 bg-white overflow-auto">
            @include('partials.notes.list')
        </aside>

        <section class="flex-1 overflow-auto bg-slate-50">
            <div class="max-w-4xl mx-auto p-8">
                @if ($note)
                    @include('partials.notes.edit-form')
                @else
                    <div class="h-full flex items-center justify-center text-slate-500">
                        Nota no encontrada
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
