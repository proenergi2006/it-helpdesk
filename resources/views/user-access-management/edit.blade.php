<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            ✏️ Edit User Access
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-100 text-rose-700 px-4 py-3">
                    <div class="font-semibold mb-1">Gagal memperbarui data</div>
                    <div class="text-sm">{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3">
                    <div class="font-semibold mb-2">Periksa kembali input Anda:</div>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                x-data="{
                    menus: {{ old('menus') ? json_encode(old('menus')) : json_encode(
                        $row->menus->map(function($m){
                            return [
                                'menu_name' => $m->menu_name,
                                'module' => $m->module,
                                'can_create' => (bool) $m->can_create,
                                'can_view' => (bool) $m->can_view,
                                'can_update' => (bool) $m->can_update,
                                'can_delete' => (bool) $m->can_delete,
                                'can_approve' => (bool) $m->can_approve,
                            ];
                        })->values()
                    ) }},
                    addMenu() {
                        this.menus.push({
                            menu_name: '',
                            module: '',
                            can_create: false,
                            can_view: true,
                            can_update: false,
                            can_delete: false,
                            can_approve: false
                        });
                    },
                    removeMenu(index) {
                        if (this.menus.length > 1) {
                            this.menus.splice(index, 1);
                        }
                    }
                }"
            >
                <form method="POST"
                      action="{{ route('user-access-management.update', $row->id) }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-white p-6 rounded-2xl shadow">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800">Edit User Access</h1>
                                <p class="text-sm text-slate-500">Ubah data akses user internal</p>
                            </div>

                            <a href="{{ route('user-access-management.index') }}"
                               class="inline-flex items-center justify-center bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-xl font-semibold shadow">
                                Kembali
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Nama User</label>
                                <input type="text" name="nama_user" value="{{ old('nama_user', $row->nama_user) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $row->email) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Role</label>
                                <input type="text" name="role" value="{{ old('role', $row->role) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Divisi</label>
                                <input type="text" name="divisi" value="{{ old('divisi', $row->divisi) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Cabang</label>
                                <input type="text" name="cabang" value="{{ old('cabang', $row->cabang) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $row->penanggung_jawab) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                                <select name="status"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    <option value="active" {{ old('status', $row->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $row->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="resign" {{ old('status', $row->status) === 'resign' ? 'selected' : '' }}>Resign</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Tanggal Resign</label>
                                <input type="date" name="tgl_resign"
                                    value="{{ old('tgl_resign', optional($row->tgl_resign)->format('Y-m-d')) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Kategori System</label>
                                <select name="kategori_system"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    @foreach (['SYOP','SERVER','ACCURATE','JPAYROLL','HELPDESK','CRS'] as $item)
                                        <option value="{{ $item }}" {{ old('kategori_system', $row->kategori_system) === $item ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Workflow Status</label>
                                <select name="workflow_status"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    <option value="draft" {{ old('workflow_status', $row->workflow_status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending_approval" {{ old('workflow_status', $row->workflow_status) === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="approved" {{ old('workflow_status', $row->workflow_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('workflow_status', $row->workflow_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-3 pt-7">
                                <input type="hidden" name="is_critical" value="0">
                                <input type="checkbox" name="is_critical" value="1"
                                    {{ old('is_critical', $row->is_critical) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                <label class="text-sm font-medium text-slate-700">Critical Access</label>
                            </div>

                            <div class="flex items-center gap-3 pt-7">
                                <input type="hidden" name="approval_ceo" value="0">
                                <input type="checkbox" name="approval_ceo" value="1"
                                    {{ old('approval_ceo', $row->approval_ceo) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                <label class="text-sm font-medium text-slate-700">Approval CEO</label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Approval At</label>
                                <input type="date" name="approval_at"
                                    value="{{ old('approval_at', optional($row->approval_at)->format('Y-m-d')) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Disabled By</label>
                                <input type="text" name="disabled_by" value="{{ old('disabled_by', $row->disabled_by) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Disabled At</label>
                                <input type="date" name="disabled_at"
                                    value="{{ old('disabled_at', optional($row->disabled_at)->format('Y-m-d')) }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-600 mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="4"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">{{ old('keterangan', $row->keterangan) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-600 mb-1">Lampiran Baru</label>
                                <input type="file" name="lampiran"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                                @if ($row->lampiran)
                                    <div class="mt-2 text-sm">
                                        Lampiran saat ini:
                                        <a href="{{ asset('storage/' . $row->lampiran) }}" target="_blank"
                                           class="text-blue-600 hover:underline font-semibold">
                                            Lihat Lampiran
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-800">Menu Akses</h2>
                                <p class="text-sm text-slate-500">Ubah daftar menu dan level akses</p>
                            </div>

                            <button type="button" @click="addMenu"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-semibold shadow">
                                + Tambah Menu
                            </button>
                        </div>

                        <template x-for="(menu, index) in menus" :key="index">
                            <div class="border border-slate-200 rounded-2xl p-4 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-semibold text-slate-700" x-text="'Menu #' + (index + 1)"></div>
                                    <button type="button" @click="removeMenu(index)"
                                        class="bg-rose-100 hover:bg-rose-200 text-rose-600 px-3 py-1 rounded-lg text-sm font-semibold">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama Menu</label>
                                        <input type="text"
                                               :name="`menus[${index}][menu_name]`"
                                               x-model="menu.menu_name"
                                               class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-600 mb-1">Module</label>
                                        <input type="text"
                                               :name="`menus[${index}][module]`"
                                               x-model="menu.module"
                                               class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="hidden" :name="`menus[${index}][can_create]`" value="0">
                                        <input type="checkbox" :name="`menus[${index}][can_create]`" value="1" x-model="menu.can_create"
                                            class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        Create
                                    </label>

                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="hidden" :name="`menus[${index}][can_view]`" value="0">
                                        <input type="checkbox" :name="`menus[${index}][can_view]`" value="1" x-model="menu.can_view"
                                            class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        View
                                    </label>

                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="hidden" :name="`menus[${index}][can_update]`" value="0">
                                        <input type="checkbox" :name="`menus[${index}][can_update]`" value="1" x-model="menu.can_update"
                                            class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        Update
                                    </label>

                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="hidden" :name="`menus[${index}][can_delete]`" value="0">
                                        <input type="checkbox" :name="`menus[${index}][can_delete]`" value="1" x-model="menu.can_delete"
                                            class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        Delete
                                    </label>

                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="hidden" :name="`menus[${index}][can_approve]`" value="0">
                                        <input type="checkbox" :name="`menus[${index}][can_approve]`" value="1" x-model="menu.can_approve"
                                            class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        Approve
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('user-access-management.index') }}"
                           class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-3 rounded-xl font-semibold">
                            Batal
                        </a>

                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-xl font-semibold shadow">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> jadi show detail ini butuh ada lampiran lihat lampiran nya juga dan yang create butuh penambahan disabled by disabled at approval at yg isinya tanggal itu kemudian need disabled bisa di checklist jika disabled by dan disabled at wajib jika yg dicentang disable  assistant to=commentary code নেই