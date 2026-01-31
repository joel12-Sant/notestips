const input = document.getElementById('q');
const notesList = document.getElementById('notesList');
const notesCount = document.getElementById('notesCount');

if (input && notesList) {
  let t;

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, (m) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;"
    }[m]));
  }

  function renderNotes(notes) {
  if (notesCount) notesCount.textContent = notes.length;

  if (!notes.length) {
    notesList.innerHTML = `
      <div class="p-8 text-center text-slate-500">
        No se encontraron notas
      </div>
    `;
    return;
  }

  notesList.innerHTML = `
    <ul role="list" class="divide-y divide-slate-200">
      ${notes.map(note => `
        <li>
          <a href="/notes/${note.id}?q=${encodeURIComponent(input.value.trim())}"
             class="block p-4 transition bg-white hover:bg-slate-50">
            <h3 class="mb-1 truncate text-slate-900">
              ${escapeHtml(note.title ?? 'Nota sin título')}
            </h3>

            <div class="flex items-center gap-2 text-xs mb-2">
              ${note.importance ? `
                <span class="inline-flex items-center gap-1 rounded px-2 py-1 ${escapeHtml(note.badge_classes)}">
                  <span aria-hidden="true">●</span>
                  ${escapeHtml(note.importance)}
                </span>
              ` : ''}

              ${note.due_date_label ? `
                <span class="inline-flex items-center gap-1 text-slate-500">
                  <span aria-hidden="true">📅</span>
                  ${escapeHtml(note.due_date_label)}
                </span>
              ` : ''}
            </div>

            <div class="flex items-center gap-1 text-xs text-slate-500">
              <span aria-hidden="true">🕒</span>
              <span>${escapeHtml(note.last_edited_label ?? 'Hace un momento')}</span>
            </div>
          </a>
        </li>
      `).join('')}
    </ul>
  `;
}


  input.addEventListener('input', () => {
    clearTimeout(t);

    t = setTimeout(async () => {
      const q = input.value.trim();

      const url = new URL(window.location.href);
      if (q) url.searchParams.set('q', q);
      else url.searchParams.delete('q');
      history.replaceState({}, '', url);

      const res = await fetch(`/notes/search?q=${encodeURIComponent(q)}`, {
        headers: { "Accept": "application/json" }
      });
      if (!res.ok) return;

      const notes = await res.json();
      renderNotes(notes);
    }, 300);
  });
}
