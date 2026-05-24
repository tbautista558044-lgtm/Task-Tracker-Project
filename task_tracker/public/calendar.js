/* =============================================
   CALENDAR.JS — Task Tracker Calendar View
   ============================================= */
(function () {

  /* ── Task lookup: "YYYY-MM-DD" → [tasks] ── */
  const tasksByDate = {};
  PHP_TASKS.forEach(task => {
    const day = task.created_at.slice(0, 10);
    if (!tasksByDate[day]) tasksByDate[day] = [];
    tasksByDate[day].push(task);
  });

  /* ── State ── */
  const today         = new Date();
  let   viewYear      = today.getFullYear();
  let   viewMonth     = today.getMonth();
  let   selectedDateStr = toDateStr(today);

  /* ── DOM refs ── */
  const monthLabel        = document.getElementById('monthLabel');
  const calDays           = document.getElementById('calDays');
  const scheduleList      = document.getElementById('scheduleList');
  const scheduleDateLabel = document.getElementById('scheduleDateLabel');
  const prevBtn           = document.getElementById('prevBtn');
  const nextBtn           = document.getElementById('nextBtn');
  const inlineAddForm     = document.getElementById('inlineAddForm');
  const hiddenDate        = document.getElementById('hiddenDate');
  const inlineTitle       = document.getElementById('inlineTitle');
  const cancelInlineAdd   = document.getElementById('cancelInlineAdd');
  const deleteModal       = document.getElementById('deleteModal');

  /* ── Navigation ── */
  prevBtn.addEventListener('click', () => adjustMonth(-1));
  nextBtn.addEventListener('click', () => adjustMonth(+1));

  function adjustMonth(delta) {
    viewMonth += delta;
    if (viewMonth < 0)  { viewMonth = 11; viewYear--; }
    if (viewMonth > 11) { viewMonth = 0;  viewYear++; }
    renderCalendar();
  }

  /* ── Inline add form ── */
  if (cancelInlineAdd) {
    cancelInlineAdd.addEventListener('click', () => {
      inlineAddForm.classList.remove('show');
      inlineTitle.value = '';
    });
  }

  function showAddForm(dateStr) {
    hiddenDate.value = dateStr;
    inlineTitle.value = '';
    document.getElementById('inlineDesc').value = '';
    inlineAddForm.classList.add('show');
    setTimeout(() => inlineTitle.focus(), 80);
  }

  /* ── Main render ── */
  function renderCalendar() {
    const monthNames = ['January','February','March','April','May','June',
      'July','August','September','October','November','December'];
    monthLabel.textContent = `${monthNames[viewMonth]} ${viewYear}`;
    calDays.innerHTML = '';

    const firstDate = new Date(viewYear, viewMonth, 1);
    let startDow = firstDate.getDay();
    startDow = (startDow === 0) ? 6 : startDow - 1;

    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

    for (let i = 0; i < startDow; i++) {
      const blank = document.createElement('div');
      blank.className = 'cal-day cal-day--blank';
      calDays.appendChild(blank);
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr    = toDateStr(new Date(viewYear, viewMonth, d));
      const isToday    = dateStr === toDateStr(today);
      const isSelected = dateStr === selectedDateStr;
      const tasks      = tasksByDate[dateStr] || [];
      const hasPending = tasks.some(t => t.status === 'pending');
      const hasDone    = tasks.some(t => t.status === 'complete');

      const cell = document.createElement('div');
      cell.className = 'cal-day'
        + (isToday    ? ' cal-day--today'    : '')
        + (isSelected ? ' cal-day--selected' : '')
        + (tasks.length ? ' cal-day--has-tasks' : '');

      let dots = '';
      if (hasPending) dots += `<span class="cal-dot cal-dot--pending"></span>`;
      if (hasDone)    dots += `<span class="cal-dot cal-dot--done"></span>`;

      cell.innerHTML = `<span class="cal-day-num">${d}</span><div class="cal-dots">${dots}</div>`;

      cell.addEventListener('click', () => {
        selectedDateStr = dateStr;
        renderCalendar();
        renderSchedule();
      });

      calDays.appendChild(cell);
    }

    renderSchedule();
  }

  /* ── Schedule panel ── */
  function renderSchedule() {
    const [y, m, d] = selectedDateStr.split('-').map(Number);
    const dateObj   = new Date(y, m - 1, d);
    const opts      = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    scheduleDateLabel.textContent = dateObj.toLocaleDateString('en-PH', opts);

    const tasks = tasksByDate[selectedDateStr] || [];

    // Always show "Add task for this day" button
    const addBtn = `<button class="btn btn-primary btn-sm cal-add-day-btn" onclick="window.calShowAdd('${selectedDateStr}')">+ Add task for this day</button>`;

    if (tasks.length === 0) {
      scheduleList.innerHTML = `
        <div class="schedule-empty">
          <div class="icon">📋</div>
          <p>No tasks on this day.</p>
          ${addBtn}
        </div>`;
      return;
    }

    const items = tasks.map((task, i) => {
      const isDone    = task.status === 'complete';
      const timeLabel = formatTime(task.created_at);
      const emoji     = getTaskEmoji(task.title);

      return `
        <div class="schedule-item ${isDone ? 'schedule-item--done' : ''}" style="animation-delay:${i * 0.05}s">
          <div class="schedule-item-icon">${emoji}</div>
          <div class="schedule-item-body">
            <div class="schedule-item-title">${escHtml(task.title)}</div>
            ${task.description
              ? `<div class="schedule-item-desc">${escHtml(task.description)}</div>`
              : ''}
            <div class="schedule-item-meta">
              <span class="badge badge-${task.status}">${isDone ? '✓ done' : '⏳ pending'}</span>
              <span class="schedule-item-time">${timeLabel}</span>
            </div>
          </div>
          <div class="schedule-item-actions">
            ${isDone
              ? `<a href="index.php?page=mark-pending&id=${task.id}&from=calendar" class="btn btn-ghost btn-sm">↩ Undo</a>`
              : `<a href="index.php?page=mark-complete&id=${task.id}&from=calendar" class="btn btn-success btn-sm">✓ Done</a>`
            }
            <a href="index.php?page=edit-task&id=${task.id}" class="btn btn-ghost btn-sm">✎ Edit</a>
            <button class="btn btn-danger btn-sm"
              onclick="openDeleteModal(${task.id}, '${escHtml(task.title).replace(/'/g,"\\'")}')">🗑</button>
          </div>
        </div>`;
    }).join('');

    scheduleList.innerHTML = items + `<div class="cal-add-row">${addBtn}</div>`;
  }

  /* ── Delete modal ── */
  window.openDeleteModal = function(id, title) {
    document.getElementById('modalDesc').textContent =
      `"${title}" will be permanently deleted.`;
    document.getElementById('confirmDeleteBtn').href =
      `index.php?page=delete-task&id=${id}&from=calendar`;
    deleteModal.classList.add('show');
  };
  window.closeDeleteModal = function() {
    deleteModal.classList.remove('show');
  };
  if (deleteModal) {
    deleteModal.addEventListener('click', e => { if (e.target === deleteModal) closeDeleteModal(); });
  }
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteModal(); });

  /* ── Expose showAdd globally for inline button ── */
  window.calShowAdd = function(dateStr) {
    selectedDateStr = dateStr;
    showAddForm(dateStr);
    renderCalendar();
    renderSchedule();
  };

  /* ── Helpers ── */
  function toDateStr(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  function formatTime(datetimeStr) {
    const [, time] = datetimeStr.split(' ');
    if (!time) return '';
    const [h, min] = time.split(':').map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${String(min).padStart(2,'0')} ${ampm}`;
  }

  function escHtml(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function getTaskEmoji(title) {
    const t = title.toLowerCase();
    if (/meet|team|call|zoom|standup/.test(t))        return '📅';
    if (/buy|shop|grocery|market/.test(t))             return '🛒';
    if (/study|read|review|notes|exam/.test(t))        return '📚';
    if (/code|fix|debug|deploy|build|project/.test(t)) return '💻';
    if (/lunch|dinner|eat|food|coffee/.test(t))        return '☕';
    if (/email|send|reply|message/.test(t))            return '📧';
    if (/design|ui|ux|figma/.test(t))                  return '🎨';
    if (/present|slide|deck|pitch/.test(t))            return '📊';
    if (/exercise|gym|run|walk|workout/.test(t))       return '🏃';
    return '✦';
  }

  /* ── Bootstrap ── */
  renderCalendar();

  // If returning from an action on calendar, show today
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('from') === 'calendar') {
    renderSchedule();
  }

})();