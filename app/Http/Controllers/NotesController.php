<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotesController extends Controller
{
    public function watch(Request $request): View
    {
        $notes = $this->baseSearchQuery($request)->get();

        $note = null;
        $noteNotFound = false;

        return view('notes.index', compact('notes', 'note', 'noteNotFound'));
    }

    public function show(Note $note, Request $request): View
    {
        $notes = $this->baseSearchQuery($request)->get();

        if (! $note || auth()->user()->cannot('view', $note)) {
            $note = null;
            $noteNotFound = true;
        } else {
            $noteNotFound = false;
        }

        return view('notes.index', compact('notes', 'note', 'noteNotFound'));
    }

    public function create(Request $request): View
    {
        $note = null;
        $notes = $this->baseSearchQuery($request)->get();

        return view('notes.create', compact('notes', 'note'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'importance' => $request->importance ?? null,
            'due_date' => $request->due_date ?? null,
        ]);

        $validated = $request->validate(
            [
                'title' => ['required', 'string', 'max:128'],
                'content' => ['required', 'string'],
                'importance' => ['nullable', Rule::in(['baja', 'media', 'alta'])],
                'due_date' => ['nullable', 'date_format:Y-m-d'],
            ],
            [
                'title.required' => 'El titulo es obligatorio',
                'content.required' => 'El contenido es obligatorio',
                'due_date.date_format' => 'La fecha debe venir como YYYY-MM-DD.',
                'due_date.after_or_equal' => 'La fecha selecionada ya paso',
                'importance.in' => 'Importancia no válida.',
            ]
        );

        $note = [
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ];

        if (array_key_exists('importance', $validated)) {
            $note['importance'] = $validated['importance'];
        }
        if (array_key_exists('due_date', $validated)) {
            $note['due_date'] = $validated['due_date'];
        }

        Note::create($note);

        return redirect()->route('notes.index')->with('status', 'created');
    }

    public function edit(Note $note, Request $request): View
    {

        $notes = $this->baseSearchQuery($request)->get();

        if (! $note || auth()->user()->cannot('view', $note)) {
            $noteNotFound = true;
            $note = null;
        } else {
            $noteNotFound = false;
        }

        return view('notes.edit', compact('notes', 'note', 'noteNotFound'));
    }

    public function update(Request $request, Note $note): RedirectResponse
    {

        $request->merge([
            'importance' => $request->importance ?? null,
            'due_date' => $request->due_date ?? null,
        ]);

        $validated = $request->validate(
            [
                'title' => ['required', 'string', 'max:128'],
                'content' => ['required', 'string'],
                'importance' => ['nullable', Rule::in(['baja', 'media', 'alta'])],
                'due_date' => ['nullable', 'date_format:Y-m-d'],
            ],
            [
                'title.required' => 'El titulo es obligatorio',
                'content.required' => 'El contenido es obligatorio',
                'due_date.date_format' => 'La fecha debe venir como YYYY-MM-DD.',
                'due_date.after_or_equal' => 'La fecha selecionada ya paso',
                'importance.in' => 'Importancia no válida.',
            ]
        );

        abort_if(! $note || auth()->user()->cannot('update', $note), 404);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'importance' => $validated['importance'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('notes.show', ['note' => $note->id])->with('status', 'updated');
    }

    public function destroy(Note $note): RedirectResponse
    {
        abort_if(! $note || auth()->user()->cannot('delete', $note), 404);
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'deleted');
    }

    public function toggleTask(Request $request, Note $note, int $taskIndex): JsonResponse
    {
        abort_if(! $note || auth()->user()->cannot('update', $note), 404);
        abort_if($taskIndex < 0, 422);

        $validated = $request->validate([
            'completed' => ['required', 'boolean'],
        ]);

        $lines = preg_split('/\R/u', $note->content ?? '') ?: [];
        $currentTaskIndex = 0;
        $updated = false;

        foreach ($lines as $lineIndex => $line) {
            if (! preg_match('/^(\s*[-*+]\s+\[)( |x|X)(\]\s?.*)$/u', $line, $matches)) {
                continue;
            }

            if ($currentTaskIndex !== $taskIndex) {
                $currentTaskIndex++;
                continue;
            }

            $lines[$lineIndex] = $matches[1].($validated['completed'] ? 'x' : ' ').$matches[3];
            $updated = true;
            break;
        }

        abort_if(! $updated, 404);

        $note->update([
            'content' => implode("\n", $lines),
        ]);

        return response()->json([
            'status' => 'ok',
        ]);
    }

    // funciones del controldor
    public function baseSearchQuery(Request $request)
    {
        $importances = ['baja', 'media', 'alta', 'none'];
        $due_date_modes = ['with', 'none', 'exact'];
        $order_bys = ['created_at', 'updated_at', 'due_date'];
        $importance = trim((string) $request->query('importance', ''));
        $due_date_mode = trim((string) $request->query('due_date_mode', ''));
        $due_date = trim((string) $request->query('due_date', ''));
        $order_by = trim((string) $request->query('order_by', ''));
        $q = trim((string) $request->query('q', ''));
        if (! in_array($importance, $importances, true)) {
            $importance = '';
        }
        if (! in_array($due_date_mode, $due_date_modes, true)) {
            $due_date_mode = '';
        }
        if (! in_array($order_by, $order_bys, true)) {
            $order_by = '';
        }

        $notesQuery = Note::where('user_id', auth()->id())
            ->select('id', 'title', 'importance', 'due_date', 'updated_at', 'created_at');

        if ($q !== '') {
            $notesQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }
        if ($importance !== '') {
            $importance === 'none'
                ? $notesQuery->whereNull('importance')
                : $notesQuery->where('importance', $importance);
        }
        if ($due_date_mode !== '') {
            if ($due_date_mode === 'none') {
                $notesQuery->whereNull('due_date');
            } elseif ($due_date_mode === 'exact' && $due_date !== '') {
                $notesQuery->where('due_date', $due_date);
            } elseif ($due_date_mode === 'with') {
                $notesQuery->whereNotNull('due_date');
            }
        }

        if ($order_by === 'due_date') {
            $notesQuery
                ->orderByRaw('due_date IS NULL ASC')
                ->orderBy('due_date', 'asc');
        } elseif ($order_by !== '') {
            $notesQuery->orderBy($order_by, 'desc');
        } else {
            $notesQuery->orderBy('updated_at', 'desc');
        }

        return $notesQuery;
    }

    public function search(Request $request): JsonResponse
    {
        $notes = $this->baseSearchQuery($request)->get()->map(function ($note) {
            $importance = $note->importance;

            $badgeClasses = match ($importance) {
                'alta' => 'bg-red-50 text-red-600',
                'media' => 'bg-amber-100 text-amber-700',
                'baja' => 'bg-blue-100 text-blue-700',
                default => 'bg-slate-100 text-slate-600',
            };

            return [
                'id' => $note->id,
                'title' => $note->title,
                'importance' => $importance,
                'badge_classes' => $badgeClasses,
                'due_date_label' => $note->due_date ? \Carbon\Carbon::parse($note->due_date)->format('d/m/Y') : null,
                'last_edited_label' => optional($note->updated_at)->diffForHumans(),
            ];
        });

        return response()->json($notes);
    }
}
