<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">📋 Kanban Board</h2>
                <div class="text-sm text-gray-500">Drag & drop project untuk update status</div>
            </div>
    
            <a href="{{ route('projects.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                List View
            </a>
        </div>
    </x-slot>
    
    <div class="py-6">
    <div class="max-w-[1600px] mx-auto px-4">
    
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    
            @foreach($statuses as $status)
                @php
                    $meta = [
                        'backlog' => ['Backlog', 'bg-slate-100', 'text-slate-700', 'border-slate-300'],
                        'todo' => ['To Do', 'bg-blue-50', 'text-blue-700', 'border-blue-300'],
                        'in_progress' => ['In Progress', 'bg-amber-50', 'text-amber-700', 'border-amber-300'],
                        'review' => ['Review', 'bg-purple-50', 'text-purple-700', 'border-purple-300'],
                        'done' => ['Done', 'bg-green-50', 'text-green-700', 'border-green-300'],
                    ][$status];
    
                    [$title, $bg, $text, $border] = $meta;
                    $count = ($projects[$status] ?? collect())->count();
                @endphp
    
                {{-- COLUMN --}}
                <div class="rounded-2xl border {{ $border }} {{ $bg }} p-3 flex flex-col">
    
                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-3 gap-2">
                        <div class="flex items-center gap-2">
                            <div class="font-semibold {{ $text }}">{{ $title }}</div>
    
                            <span data-count="{{ $status }}"
                                  class="text-xs px-2.5 py-1 rounded-full bg-white/80 text-gray-700
                                         shadow-sm ring-1 ring-black/5">
                                {{ $count }}
                            </span>
                        </div>
    
                        {{-- QUICK ADD --}}
                        <button
                            type="button"
                            class="quick-add h-8 w-8 rounded-lg bg-white/80 hover:bg-white
                                   ring-1 ring-black/5 shadow-sm transition"
                            data-status="{{ $status }}"
                            data-title="{{ $title }}"
                            title="Quick Add">
                            +
                        </button>
                    </div>
    
                    {{-- DROP ZONE --}}
                    <div id="col-{{ $status }}"
                         class="space-y-3 flex-1 min-h-[200px]"
                         data-status="{{ $status }}">
    
                        @foreach($projects[$status] ?? [] as $p)
                            <div class="kanban-card bg-white rounded-xl shadow-sm hover:shadow-md
                                        transition cursor-move border-l-4 {{ $border }}"
                                 data-id="{{ $p->id }}">
    
                                <div class="p-3 space-y-1">
                                    <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                    <div class="text-xs text-gray-500">#PRJ-{{ $p->id }}</div>
    
                                    @if($p->assignees->count())
                                        <div class="flex flex-wrap gap-1 pt-1">
                                            @foreach($p->assignees->take(3) as $a)
                                                <span class="px-2 py-0.5 text-[10px] rounded
                                                             bg-gray-100 text-gray-700">
                                                    {{ $a->name }}
                                                </span>
                                            @endforeach
                                            @if($p->assignees->count() > 3)
                                                <span class="text-[10px] text-gray-400">
                                                    +{{ $p->assignees->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
    
                                    @if($p->due_date)
                                        <div class="text-[10px] text-gray-500 pt-1">
                                            ⏰ {{ $p->due_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
    
        </div>
    </div>
    </div>
    
    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <script>
        const USERS = @json($users->map(fn($u)=>['id'=>$u->id,'name'=>$u->name])->values());
        const QUICK_ADD_URL = "{{ route('projects.board.quickAdd') }}";
        const CSRF = "{{ csrf_token() }}";
        
        function refreshCounts() {
          document.querySelectorAll('[data-status]').forEach(col => {
            const status = col.dataset.status;
            const count = col.querySelectorAll('.kanban-card').length;
            const badge = document.querySelector(`[data-count="${status}"]`);
            if (badge) badge.textContent = count;
          });
        }
        
        function escapeHtml(str) {
          return String(str ?? '').replace(/[&<>"']/g, s => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
          }[s]));
        }
        
        function renderCardFromJson(p) {
          const assignees = (p.assignees || []).map(n =>
            `<span class="px-2 py-0.5 text-[10px] rounded bg-gray-100 text-gray-700">${escapeHtml(n)}</span>`
          ).join('');
          const more = (p.assignees_more || 0) > 0 ? `<span class="text-[10px] text-gray-400">+${p.assignees_more}</span>` : '';
          const due = p.due_date ? `<div class="text-[10px] text-gray-500 pt-1">⏰ ${escapeHtml(p.due_date)}</div>` : '';
        
          return `
            <div class="kanban-card bg-white rounded-xl shadow-sm hover:shadow-md transition cursor-move border-l-4"
                 data-id="${p.id}">
              <div class="p-3 space-y-1">
                <div class="font-medium text-gray-900 leading-snug">${escapeHtml(p.name)}</div>
                <div class="text-xs text-gray-500">#PRJ-${p.id}</div>
                ${(assignees || more) ? `<div class="flex flex-wrap gap-1 pt-1">${assignees}${more}</div>` : ''}
                ${due}
              </div>
            </div>
          `;
        }
        
        document.querySelectorAll('.quick-add').forEach(btn => {
          btn.addEventListener('click', async () => {
            const status = btn.dataset.status;
            const title = btn.dataset.title;
        
            const userOptions = USERS.map(u => `<option value="${u.id}">${escapeHtml(u.name)}</option>`).join('');
        
            const { value } = await Swal.fire({
              title: `Quick Add → ${title}`,
              customClass: {
                popup: 'qa-modal',
                title: 'qa-title',
                htmlContainer: 'qa-body',
                actions: 'qa-actions',
              },
              width: 560,
              showCancelButton: true,
              confirmButtonText: 'Create',
              cancelButtonText: 'Cancel',
              focusConfirm: false,
              html: `
                <div class="qa-grid">
                  <div class="full">
                    <div class="qa-label">Nama Project</div>
                    <input id="qa_name" class="qa-input" placeholder="Contoh: Helpdesk Revamp"/>
                  </div>
        
                  <div>
                    <div class="qa-label">Due Date (opsional)</div>
                    <input id="qa_due" type="date" class="qa-input"/>
                  </div>
        
                  <div>
                    <div class="qa-label">Assignee (opsional)</div>
                    <select id="qa_users" multiple class="qa-select">
                      ${userOptions}
                    </select>
                    <div class="qa-help">Ketik untuk search, bisa pilih lebih dari 1</div>
                  </div>
                </div>
              `,
              didOpen: () => {
                // TomSelect inside modal
                const el = document.getElementById('qa_users');
                if (el && window.TomSelect) {
                  // destroy jika pernah ada (safety)
                  if (el.tomselect) el.tomselect.destroy();
        
                  new window.TomSelect(el, {
                    plugins: ['remove_button'],
                    placeholder: 'Pilih assignee…',
                    closeAfterSelect: false,
                    create: false,
                  });
                }
              },
              preConfirm: () => {
                const name = document.getElementById('qa_name').value.trim();
                const due_date = document.getElementById('qa_due').value || null;
        
                const sel = document.getElementById('qa_users');
                const assignees = sel.tomselect
                  ? sel.tomselect.items.map(v => Number(v))
                  : Array.from(sel.selectedOptions).map(o => Number(o.value));
        
                if (!name) {
                  Swal.showValidationMessage('Nama project wajib diisi.');
                  return;
                }
                return { name, due_date, assignees, status };
              }
            });
        
            if (!value) return;
        
            const res = await fetch(QUICK_ADD_URL, {
              method: "POST",
              headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF },
              body: JSON.stringify(value)
            });
        
            if (!res.ok) {
              let err = {};
              try { err = await res.json(); } catch {}
              Swal.fire({ icon:'error', title:'Gagal', text: err?.message || 'Tidak bisa membuat project.' });
              return;
            }
        
            const json = await res.json();
            const col = document.getElementById(`col-${status}`);
            if (col) col.insertAdjacentHTML('beforeend', renderCardFromJson(json.project));
        
            refreshCounts();
        
            Swal.fire({
              icon: 'success',
              title: 'Created',
              text: 'Project berhasil dibuat.',
              timer: 1000,
              showConfirmButton: false
            });
          });
        });
        </script>
        
    </x-app-layout>
    