<?php

use App\Models\Note;
use App\Models\User;

function crearUsuarioNotas(string $username = 'notes-user', string $password = 'Password123!'): User
{
    return User::create([
        'username' => $username,
        'password' => $password,
    ]);
}

test('lista solo notas del usuario autenticado', function () {
    $owner = crearUsuarioNotas('owner');
    $other = crearUsuarioNotas('other');

    Note::create([
        'user_id' => $owner->id,
        'title' => 'Owner note',
        'content' => 'Only owner should see this',
    ]);

    Note::create([
        'user_id' => $other->id,
        'title' => 'Other note',
        'content' => 'Should not appear',
    ]);

    $this->actingAs($owner)
        ->get('/notes')
        ->assertOk()
        ->assertSee('Owner note')
        ->assertDontSee('Other note');
});

test('crea una nota con campos opcionales', function () {
    $user = crearUsuarioNotas();

    $this->actingAs($user)
        ->post('/notes', [
            'title' => 'New note',
            'content' => 'My content',
            'importance' => 'alta',
            'due_date' => '2026-03-15',
        ])
        ->assertRedirect(route('notes.index'));

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'title' => 'New note',
        'content' => 'My content',
        'importance' => 'alta',
        'due_date' => '2026-03-15 00:00:00',
    ]);
});

test('valida campos requeridos al crear una nota', function () {
    $user = crearUsuarioNotas();

    $this->actingAs($user)
        ->post('/notes', [
            'title' => '',
            'content' => '',
            'importance' => 'invalid',
            'due_date' => 'not-a-date',
        ])
        ->assertSessionHasErrors(['title', 'content', 'importance', 'due_date']);
});

test('muestra el detalle de una nota propia', function () {
    $user = crearUsuarioNotas();
    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Visible detail',
        'content' => '# Markdown title',
    ]);

    $this->actingAs($user)
        ->get("/notes/{$note->id}")
        ->assertOk()
        ->assertSee('Visible detail');
});

test('no permite ver el detalle de una nota ajena', function () {
    $owner = crearUsuarioNotas('owner2');
    $intruder = crearUsuarioNotas('intruder2');

    $note = Note::create([
        'user_id' => $owner->id,
        'title' => 'Private note',
        'content' => 'Private content',
    ]);

    $this->actingAs($intruder)
        ->get("/notes/{$note->id}")
        ->assertOk()
        ->assertSee('Nota no encontrada')
        ->assertDontSee('Private content');
});

test('actualiza una nota propia', function () {
    $user = crearUsuarioNotas('editor');
    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Before title',
        'content' => 'Before content',
    ]);

    $this->actingAs($user)
        ->put("/notes/{$note->id}", [
            'title' => 'After title',
            'content' => 'After content',
            'importance' => 'media',
            'due_date' => '2026-03-20',
        ])
        ->assertRedirect(route('notes.show', ['note' => $note->id]));

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'After title',
        'content' => 'After content',
        'importance' => 'media',
        'due_date' => '2026-03-20 00:00:00',
    ]);
});

test('bloquea la actualizacion de una nota ajena', function () {
    $owner = crearUsuarioNotas('owner3');
    $intruder = crearUsuarioNotas('intruder3');

    $note = Note::create([
        'user_id' => $owner->id,
        'title' => 'Cannot edit me',
        'content' => 'No access',
    ]);

    $this->actingAs($intruder)
        ->put("/notes/{$note->id}", [
            'title' => 'Hacked',
            'content' => 'Hacked content',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Cannot edit me',
    ]);
});

test('elimina una nota propia', function () {
    $user = crearUsuarioNotas('deleter');
    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Delete me',
        'content' => 'to be deleted',
    ]);

    $this->actingAs($user)
        ->delete("/notes/{$note->id}")
        ->assertRedirect(route('notes.index'));

    $this->assertDatabaseMissing('notes', [
        'id' => $note->id,
    ]);
});

test('bloquea la eliminacion de una nota ajena', function () {
    $owner = crearUsuarioNotas('owner4');
    $intruder = crearUsuarioNotas('intruder4');

    $note = Note::create([
        'user_id' => $owner->id,
        'title' => 'Protected delete',
        'content' => 'No delete',
    ]);

    $this->actingAs($intruder)
        ->delete("/notes/{$note->id}")
        ->assertNotFound();

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
    ]);
});

test('el endpoint de busqueda filtra por texto y devuelve json', function () {
    $user = crearUsuarioNotas('searcher');

    Note::create([
        'user_id' => $user->id,
        'title' => 'Laravel Notes',
        'content' => 'Framework notes',
        'importance' => 'alta',
    ]);

    Note::create([
        'user_id' => $user->id,
        'title' => 'Cooking',
        'content' => 'Recipes',
    ]);

    $this->actingAs($user)
        ->getJson('/notes/search?q=laravel')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'title' => 'Laravel Notes',
            'importance' => 'alta',
        ]);
});

test('la busqueda soporta el filtro de importancia none', function () {
    $user = crearUsuarioNotas('importance-filter');

    Note::create([
        'user_id' => $user->id,
        'title' => 'Without importance',
        'content' => 'content',
        'importance' => null,
    ]);

    Note::create([
        'user_id' => $user->id,
        'title' => 'With importance',
        'content' => 'content',
        'importance' => 'baja',
    ]);

    $this->actingAs($user)
        ->getJson('/notes/search?importance=none')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'title' => 'Without importance',
            'importance' => null,
        ]);
});

test('la busqueda soporta filtro por fecha exacta', function () {
    $user = crearUsuarioNotas('date-filter');

    $matchingNote = Note::create([
        'user_id' => $user->id,
        'title' => 'Match date',
        'content' => 'content',
        'due_date' => '2026-03-10',
    ]);

    Note::create([
        'user_id' => $user->id,
        'title' => 'Other date',
        'content' => 'content',
        'due_date' => '2026-03-11',
    ]);

    $queryDueDate = urlencode((string) $matchingNote->getRawOriginal('due_date'));

    $this->actingAs($user)
        ->getJson("/notes/search?due_date_mode=exact&due_date={$queryDueDate}")
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'title' => 'Match date',
        ]);
});

test('marca y desmarca tareas markdown', function () {
    $user = crearUsuarioNotas('task-owner');

    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Checklist',
        'content' => "- [ ] Task A\n- [x] Task B",
    ]);

    $this->actingAs($user)
        ->patchJson("/notes/{$note->id}/tasks/0", [
            'completed' => true,
        ])
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
        ]);

    expect($note->fresh()->content)->toContain('- [x] Task A');
});

test('devuelve 404 al alternar un indice de tarea markdown inexistente', function () {
    $user = crearUsuarioNotas('task-owner-2');

    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Checklist',
        'content' => '- [ ] Only one task',
    ]);

    $this->actingAs($user)
        ->patchJson("/notes/{$note->id}/tasks/5", [
            'completed' => true,
        ])
        ->assertNotFound();
});

test('devuelve 422 cuando el indice de tarea es negativo', function () {
    $user = crearUsuarioNotas('task-owner-3');

    $note = Note::create([
        'user_id' => $user->id,
        'title' => 'Checklist',
        'content' => '- [ ] Task',
    ]);

    $this->actingAs($user)
        ->patchJson("/notes/{$note->id}/tasks/-1", [
            'completed' => true,
        ])
        ->assertStatus(422);
});

test('bloquea alternar tareas en una nota ajena', function () {
    $owner = crearUsuarioNotas('task-owner-4');
    $intruder = crearUsuarioNotas('task-intruder-4');

    $note = Note::create([
        'user_id' => $owner->id,
        'title' => 'Private checklist',
        'content' => '- [ ] Task',
    ]);

    $this->actingAs($intruder)
        ->patchJson("/notes/{$note->id}/tasks/0", [
            'completed' => true,
        ])
        ->assertNotFound();
});
