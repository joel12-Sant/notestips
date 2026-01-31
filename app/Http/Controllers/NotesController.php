<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotesController extends Controller
{
    public function watch($note_id = null)
    {
        $notes = Note::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->select('id', 'title', 'importance', 'due_date', 'updated_at')->get();

        $noteNotFound = false;
        if ($note_id != null) {
            $selectedNote = Note::where('user_id', auth()->id())
                ->select('id', 'title', 'content', 'importance', 'due_date', 'updated_at')
                ->find($note_id);
        } else {
            $noteNotFound = true;
            $selectedNote = null;
        }

        return view('notes.index', compact('notes', 'selectedNote', 'noteNotFound'));
    }

    public function create()
    {
        $notes = Note::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->select('id', 'title', 'content', 'importance', 'due_date', 'updated_at')->get();

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
                'due_date' => ['nullable', 'date_format:Y-m-d'], // ,'after_or_equal:today'],
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

    public function edit($note_id = null)
    {

        $notes = Note::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->select('id', 'title', 'content', 'importance', 'due_date', 'updated_at')->get();

        $noteNotFound = true;
        if ($note_id) {
            $selectedNote = Note::where('user_id', auth()->id())
                ->select('id', 'title', 'content', 'importance', 'due_date')
                ->find($note_id);
        } else {
            $selectedNote = null;
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
                'due_date' => ['nullable', 'date_format:Y-m-d'], // ,'after_or_equal:today'],
            ],
            [
                'title.required' => 'El titulo es obligatorio',
                'content.required' => 'El contenido es obligatorio',
                'due_date.date_format' => 'La fecha debe venir como YYYY-MM-DD.',
                'due_date.after_or_equal' => 'La fecha selecionada ya paso',
                'importance.in' => 'Importancia no válida.',
            ]
        );

        $note = Note::where('user_id', auth()->id())->findOrFail($note_id);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'importance' => $validated['importance'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('notes.show', ['note_id' => $note->id])->with('status', 'updated');
    }
}
