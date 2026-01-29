@php
    $importance = $selectedNote->importance ?? null;
    $dueDate = $selectedNote->due_date ?? null;

    $badgeClasses = match ($importance) {
        'alta' => 'bg-red-50 text-red-600',
        'media' => 'bg-amber-100 text-amber-700',
        'baja' => 'bg-blue-100 text-blue-700',
        default => 'bg-slate-100 text-slate-600',
    };

    $lastEdited = $selectedNote->updated_at ?? now();
    $lastEditedLabel = method_exists($lastEdited, 'diffForHumans') ? $lastEdited->diffForHumans() : 'Hace un momento';

    $dueDateLabel = $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : null;
@endphp

<div class="max-w-4xl mx-auto p-8">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-4xl font-semibold text-slate-900">
                {{ $selectedNote->title ?? 'Nota sin título' }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                <span class="inline-flex items-center gap-2">
                    <x-icons.clock class="w-4 h-4" />
                    {{ $lastEditedLabel }}
                </span>

                @if (!empty($importance))
                    <span class="inline-flex items-center gap-2 rounded px-3 py-1 {{ $badgeClasses }}">
                        <span aria-hidden="true">●</span>
                        {{ ucfirst($importance) }}
                    </span>
                @endif

                @if (!empty($dueDateLabel))
                    <span class="inline-flex items-center gap-2">
                        <x-icons.calendar class="w-4 h-4" />
                        {{ $dueDateLabel }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="#{{--  --}}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                aria-label="Editar">
                <x-icons.edit class="w-5 h-5" />
            </a>

            <form method="POST" action="#{{--  --}}"
                onsubmit="return confirm('¿Seguro que quieres eliminar esta nota?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition"
                    aria-label="Eliminar">
                    <x-icons.trash class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 text-slate-900 whitespace-pre-wrap leading-relaxed">{{ $selectedNote->content }}</div>

</div>
