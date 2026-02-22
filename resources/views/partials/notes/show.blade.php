@php
    $importance = $note->importance ?? null;
    $dueDate = $note->due_date ?? null;
    $renderedContent = \Illuminate\Support\Str::markdown($note->content ?? '', [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);

    $badgeClasses = match ($importance) {
        'alta' => 'bg-red-50 text-red-600',
        'media' => 'bg-amber-100 text-amber-700',
        'baja' => 'bg-blue-100 text-blue-700',
        default => 'bg-slate-100 text-slate-600',
    };

    $lastEdited = $note->updated_at ?? now();
    $lastEditedLabel = method_exists($lastEdited, 'diffForHumans') ? $lastEdited->diffForHumans() : 'Hace un momento';

    $dueDateLabel = $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : null;
@endphp

<div class="max-w-4xl mx-auto p-8">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-4xl font-semibold text-slate-900">
                {{ $note->title ?? 'Nota sin título' }}
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
            <a href="{{ route('notes.edit', $note->id) }}"
                class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition" aria-label="Editar">
                <x-icons.edit class="w-5 h-5" />
            </a>

            <form method="POST" action="{{ route('notes.destroy', $note->id) }}"
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

    <article id="note-markdown-content"
        data-toggle-task-url-template="{{ route('notes.tasks.toggle', ['note' => $note->id, 'taskIndex' => '__TASK_INDEX__']) }}"
        data-csrf-token="{{ csrf_token() }}"
        class="mt-8 text-slate-900 leading-relaxed wrap-break-word
            [&_h1]:text-3xl [&_h1]:font-semibold [&_h1]:mt-6 [&_h1]:mb-3
            [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:mt-6 [&_h2]:mb-3
            [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:mt-5 [&_h3]:mb-2
            [&_p]:my-3 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:my-3
            [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:my-3
            [&_li]:my-1 [&_blockquote]:border-l-4 [&_blockquote]:border-slate-300
            [&_blockquote]:pl-4 [&_blockquote]:italic [&_code]:bg-slate-100 [&_code]:px-1
            [&_code]:rounded [&_pre]:bg-slate-100 [&_pre]:text-slate-900 [&_pre]:p-4
            [&_pre]:rounded-lg [&_pre]:overflow-auto [&_a]:text-blue-600 [&_a]:underline">
        {!! $renderedContent !!}
    </article>

</div>

@push('scripts')
    <script>
        (() => {
            const container = document.getElementById('note-markdown-content');
            if (!container) return;

            const urlTemplate = container.dataset.toggleTaskUrlTemplate;
            const csrfToken = container.dataset.csrfToken;
            const taskCheckboxes = Array.from(container.querySelectorAll('li input[type="checkbox"]'));

            taskCheckboxes.forEach((checkbox, taskIndex) => {
                checkbox.disabled = false;
                checkbox.dataset.taskIndex = String(taskIndex);
                checkbox.classList.add('cursor-pointer');
            });

            container.addEventListener('change', async (event) => {
                const checkbox = event.target.closest('input[type="checkbox"][data-task-index]');
                if (!checkbox || checkbox.dataset.syncing === '1') return;

                const taskIndex = checkbox.dataset.taskIndex;
                const checked = checkbox.checked;
                checkbox.dataset.syncing = '1';

                try {
                    const response = await fetch(urlTemplate.replace('__TASK_INDEX__', taskIndex), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            completed: checked,
                        }),
                    });

                    if (!response.ok) {
                        checkbox.checked = !checked;
                    }
                } catch (error) {
                    checkbox.checked = !checked;
                } finally {
                    delete checkbox.dataset.syncing;
                }
            });
        })();
    </script>
@endpush
