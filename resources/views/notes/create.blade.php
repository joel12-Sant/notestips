@extends('layout.notes.app')

@section('title', 'Crear Nota | NotesTips')

@section('main-content')
    <div class="h-full flex overflow-hidden">
        <aside class="w-80 shrink-0 border-r border-slate-200 bg-white overflow-auto">
            @include('partials.notes.list')
        </aside>

        <section class="flex-1 overflow-auto bg-slate-50">
            <div class="max-w-4xl mx-auto p-8">
                @include('partials.notes.create-form')
            </div>
        </section>
    </div>
@endsection
