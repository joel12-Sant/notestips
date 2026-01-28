<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotesController extends Controller
{
    public function watch()
    {
        $notes = Note::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->select('id', 'title', 'content', 'importance', 'due_date', 'updated_at')->get();

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
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

        $data = [
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ];

        if (array_key_exists('importance', $validated)) {
            $data['importance'] = $validated['importance'];
        }
        if (array_key_exists('due_date', $validated)) {
            $data['due_date'] = $validated['due_date'];
        }

        Note::create($data);

        return redirect()->route('notes')->with('status', 'created');

    }
}
