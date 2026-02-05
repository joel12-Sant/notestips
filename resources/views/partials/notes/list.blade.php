@section('page', 'list')

<nav aria-label="Lista de notas" class="h-full">
    <div class="sticky top-0 z-10 bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-slate-900">Notas</h2>

            <p class="mt-1 text-xs text-slate-500">
                <span id="notesCount">{{ $notes->count() }}</span>
                notas
            </p>
        </div>
        <div id="notesList">
            @if ($notes->isEmpty())
                <div class="p-8 text-center text-slate-500">
                    No se encontraron notas
                </div>
            @else
                <ul role="list" class="divide-y divide-slate-200">
                    @foreach ($notes as $s_note)
                        @php
                            $isActive = $note && $note->id == $s_note->id;

                            $importance = $s_note->importance;
                            $due_date = $s_note->due_date
                                ? \Carbon\Carbon::parse($s_note->due_date)->format('d/m/Y')
                                : null;

                            $badgeClasses = match ($importance) {
                                'alta' => 'bg-red-50 text-red-600',
                                'media' => 'bg-amber-100 text-amber-700',
                                'baja' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-100 text-slate-600',
                            };

                            $lastEdited = $s_note->updated_at ?? now();
                            $lastEditedLabel = method_exists($lastEdited, 'diffForHumans')
                                ? $lastEdited->diffForHumans()
                                : 'Hace un momento';
                        @endphp

                        <li>
                            <a href="{{ route('notes.show', ['note' => $s_note->id] + (request('q') ? ['q' => request('q')] : []) + (request('importance') ? ['importance' => request('importance')] : [])) }}"
                                class="block p-4 transition
                               {{ $isActive ? 'bg-blue-100' : 'bg-white hover:bg-slate-50' }}"
                                @if ($isActive) aria-current="true" @endif>
                                <h3 class="mb-1 truncate text-slate-900">
                                    {{ $s_note->title ?? 'Nota sin título' }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs mb-2">
                                    @if (!empty($importance))
                                        <span
                                            class="inline-flex items-center gap-1 rounded px-2 py-1 {{ $badgeClasses }}">
                                            <span aria-hidden="true">●</span>
                                            {{ $importance }}
                                        </span>
                                    @endif
                                    @if (!empty($due_date))
                                        <span class="inline-flex items-center gap-1 text-slate-500">
                                            <span aria-hidden="true"><x-icons.calendar class="w-1 h-1" />
                                            </span>
                                            {{ $due_date }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1 text-xs text-slate-500">
                                    <span aria-hidden="true"><x-icons.clock class="w-1 h1" /></span>
                                    <span>{{ $lastEditedLabel }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
</nav>
