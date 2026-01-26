<div class="">
    <h4>NotesTips</h4>
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">
            Cerrar sesion
        </button>
    </form>
    <form action="{{ route('notes.create') }}" method="GET">
        <button type="submit">
            Crear Nota
        </button>
    </form>
</div>
