const input = document.getElementById('q');
const select = document.getElementById('importance');
const dateMode = document.getElementById('due_date_mode');
const date = document.getElementById('due_date');
const notesList = document.getElementById('notesList');
const notesCount = document.getElementById('notesCount');
const chipsEl = document.getElementById('filterChips');

if (notesList) {
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

    const currentQ = input ? input.value.trim() : "";
    const currentI = select ? select.value.trim() : "";
    const currentMode = dateMode ? dateMode.value.trim() : "";
    const currentD = date ? date.value.trim() : "";

    notesList.innerHTML = `
      <ul role="list" class="divide-y divide-slate-200">
        ${notes.map(note => {
          const params = new URLSearchParams();

          if (currentQ) params.set("q", currentQ);
          if (currentI && currentI !== "0") params.set("importance", currentI);

          if (currentMode) params.set("due_date_mode", currentMode);
          if (currentMode === "with" && currentD) params.set("due_date", currentD);

          const qs = params.toString();
          const href = `/notes/${note.id}` + (qs ? `?${qs}` : "");

          return `
            <li>
              <a href="${href}"
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
          `;
        }).join('')}
      </ul>
    `;
  }

  function syncDueDateUI() {
    if (!dateMode || !date) return;
    const mode = dateMode.value.trim();
    const enabled = mode === "with";
    date.disabled = !enabled;
    date.classList.toggle("opacity-50", !enabled);
    date.classList.toggle("cursor-not-allowed", !enabled);
    if (!enabled) date.value = "";
  }

  async function fetchAndRender() {
    const q = input ? input.value.trim() : "";
    const importance = select ? select.value.trim() : "";
    const due_date_mode = dateMode ? dateMode.value.trim() : "";
    const due_date = date ? date.value.trim() : "";

    const url = new URL(window.location.href);
    if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
    if (importance) url.searchParams.set('importance', importance); else url.searchParams.delete('importance');

    if (due_date_mode) url.searchParams.set('due_date_mode', due_date_mode);
    else url.searchParams.delete('due_date_mode');

    if (due_date_mode === "with" && due_date) url.searchParams.set('due_date', due_date);
    else url.searchParams.delete('due_date');

    history.replaceState({}, '', url);

    const apiUrl = new URL('/notes/search', window.location.origin);
    if (q) apiUrl.searchParams.set('q', q);
    if (importance) apiUrl.searchParams.set('importance', importance);

    if (due_date_mode) apiUrl.searchParams.set('due_date_mode', due_date_mode);
    if (due_date_mode === "with" && due_date) apiUrl.searchParams.set('due_date', due_date);

    const res = await fetch(apiUrl.toString(), {
      headers: { "Accept": "application/json" }
    });
    if (!res.ok) return;

    const notes = await res.json();
    renderNotes(notes);
    renderFilterChips();
  }

  if (input) {
    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(fetchAndRender, 300);
    });
  }

  if (select) {
    select.addEventListener('change', () => {
      clearTimeout(t);
      fetchAndRender();
    });
  }

  if (dateMode) {
    dateMode.addEventListener('change', () => {
      clearTimeout(t);
      syncDueDateUI();
      fetchAndRender();
    });
  }

  if (date) {
    date.addEventListener('input', () => {
      clearTimeout(t);
      fetchAndRender();
    });
  }

  function formatDateYYYYMMDD(yyyyMMdd) {
    if (!yyyyMMdd) return "";
    const [y, m, d] = yyyyMMdd.split("-");
    return (y && m && d) ? `${d}/${m}/${y}` : yyyyMMdd;
  }

  function chip(label, value, onClear) {
    return `
      <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-700">
        <span class="truncate max-w-60">${escapeHtml(label)}: <strong>${escapeHtml(value)}</strong></span>
        <button type="button"
          class="ml-1 inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-500 hover:bg-slate-200 hover:text-slate-700"
          data-clear="${escapeHtml(onClear)}"
          aria-label="Quitar filtro ${escapeHtml(label)}">✕</button>
      </span>
    `;
  }

  function renderFilterChips() {
    if (!chipsEl) return;

    const currentQ = input ? input.value.trim() : "";
    const currentI = select ? select.value.trim() : "";
    const currentMode = dateMode ? dateMode.value.trim() : "";
    const currentD = date ? date.value.trim() : "";

    const chips = [];

    if (currentQ) chips.push(chip("Búsqueda", currentQ, "q"));
    if (currentI) chips.push(chip("Importancia", currentI, "importance"));

    if (currentMode === "with") {
      if (currentD) chips.push(chip("Fecha", formatDateYYYYMMDD(currentD), "due_date"));
      else chips.push(chip("Fecha", "Con fecha", "due_date_mode"));
    } else if (currentMode === "none") {
      chips.push(chip("Fecha", "Sin fecha", "due_date_mode"));
    }

    if (!chips.length) {
      chipsEl.classList.add("hidden");
      chipsEl.innerHTML = "";
      return;
    }

    chipsEl.classList.remove("hidden");
    chipsEl.innerHTML = `
      <div class="flex flex-wrap items-center gap-2">
        ${chips.join("")}
        <button type="button"
          class="ml-auto text-sm text-blue-600 hover:underline"
          data-clear="all">
          Limpiar todo
        </button>
      </div>
    `;
  }

  chipsEl?.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-clear]");
    if (!btn) return;

    const key = btn.getAttribute("data-clear");
    if (key === "q" && input) input.value = "";
    if (key === "importance" && select) select.value = "";
    if (key === "due_date_mode" && dateMode) dateMode.value = "";
    if (key === "due_date" && date) date.value = "";

    if (key === "all") {
      if (input) input.value = "";
      if (select) select.value = "";
      if (dateMode) dateMode.value = "";
      if (date) date.value = "";
    }

    syncDueDateUI();
    fetchAndRender();
  });

  syncDueDateUI();
}
