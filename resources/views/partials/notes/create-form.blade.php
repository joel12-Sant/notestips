<form action="{{ route('notes') }}" method="POST">
    @csrf
    <label for="title">titulo</label>
    <input type="text" name="title" value="{{ old('title') }}">
    @error('title')
        <div>{{ $message }}</div>
    @enderror
    <label for="content">Contenido</label>
    <textarea id="content" name="content">{{ old('content') }}</textarea>
    @error('content')
        <div>{{ $message }}</div>
    @enderror
    <button type="submit">Guardar nota</button>
</form>
