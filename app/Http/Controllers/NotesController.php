<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotesController extends Controller
{
    public function baseSearchQuery(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $notesQuery = Note::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->select('id', 'title', 'importance', 'due_date', 'updated_at');

        if ($q !== '') {
            $notesQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        return $notesQuery;
    }

    public function search(Request $request)
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

    public function watch(Request $request)
    {
        $notes = $this->baseSearchQuery($request)->get();

        $selectedNote = null;
        $noteNotFound = false;

        return view('notes.index', compact('notes', 'selectedNote', 'noteNotFound'));
    }

    public function show($note_id, Request $request)
    {
        $notes = $this->baseSearchQuery($request)->get();

        $selectedNote = Note::select('id', 'user_id', 'title', 'content', 'importance', 'due_date', 'updated_at')
            ->find($note_id);
        if (! $selectedNote || auth()->user()->cannot('view', $selectedNote)) {
            $selectedNote = null;
            $noteNotFound = true;
        } else {
            $noteNotFound = false;
        }

        return view('notes.index', compact('notes', 'selectedNote', 'noteNotFound'));
    }

    public function create(Request $request)
    {

        $notes = $this->baseSearchQuery($request)->get();

        return view('notes.create', compact('notes'));
    }

    public function store(Request $request)
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

    public function edit($note_id, Request $request)
    {

        $notes = $this->baseSearchQuery($request)->get();

        $selectedNote = Note::select('id', 'user_id', 'title', 'content', 'importance', 'due_date')
            ->find($note_id);

        if (! $selectedNote || auth()->user()->cannot('view', $selectedNote)) {
            $noteNotFound = true;
            $selectedNote = null;
        } else {
            $noteNotFound = false;
        }

        return view('notes.edit', compact('notes', 'selectedNote', 'noteNotFound'));
    }

    public function update(Request $request, $note_id)
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

        $note = Note::find($note_id);

        abort_if(! $note || auth()->user()->cannot('update', $note), 404);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'importance' => $validated['importance'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('notes.show', ['note_id' => $note->id])->with('status', 'updated');
    }

    public function destroy($note_id)
    {
        $note = Note::find($note_id);
        abort_if(! $note || auth()->user()->cannot('delete', $note), 404);
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'deleted');
    }
}
