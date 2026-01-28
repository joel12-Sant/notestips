    <form action="{{ route('notes.store') }}" method="POST" class="max-w-4xl mx-auto p-8">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="title" class="sr-only">Título de la nota</label>
                <input id="title" type="text" name="title" value="{{ old('title', 'Titulo de Ejemplo') }}"
                    placeholder="Título de la nota"
                    class="w-full text-3xl px-4 py-2 rounded-lg border bg-slate-50 text-slate-900 transition
                        border-slate-200
                        focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20
                        @error('title') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                    @error('title') aria-invalid="true" aria-describedby="title_error" @enderror>
                @error('title')
                    <p id="title_error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <label for="importance" class="block text-sm mb-2 text-slate-500">
                        Importancia
                    </label>
                    <select id="importance" name="importance"
                        class="w-full px-4 py-2 rounded-lg border bg-slate-50 text-slate-900 transition
                            border-slate-200
                            focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20
                            @error('importance') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        @error('importance') aria-invalid="true" aria-describedby="importance_error" @enderror>
                        <option value="">-- Sin importancia --</option>
                        <option value="alta" {{ old('importance') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ old('importance') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="baja" {{ old('importance') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                    @error('importance')
                        <p id="importance_error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1">
                    <label for="due_date" class="block text-sm mb-2 text-slate-500">
                        Fecha de realización
                    </label>
                    <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full px-4 py-2 rounded-lg border bg-slate-50 text-slate-900 transition
                            border-slate-200
                            focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20
                            @error('due_date') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        @error('due_date') aria-invalid="true" aria-describedby="due_date_error" @enderror>
                    @error('due_date')
                        <p id="due_date_error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="content" class="sr-only">Contenido</label>
                <textarea id="content" name="content" placeholder="Escribe tu nota aquí..."
                    class="w-full h-96 px-4 py-2 rounded-lg border bg-slate-50 text-slate-900 transition resize-none
                        border-slate-200
                        focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20
                        @error('content') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                    @error('content') aria-invalid="true" aria-describedby="content_error" @enderror>{{ old('content', 'Contenido de ejemplo, puedes editarlo o crear una nueva nota') }}</textarea>

                @error('content')
                    <p id="content_error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-6 py-2 rounded-lg transition text-white bg-green-600 hover:bg-green-700
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2">
                    Guardar
                </button>

                <a href="{{ url()->previous() }}"
                    class="px-6 py-2 rounded-lg transition border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-200
                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
