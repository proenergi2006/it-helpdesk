<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            ➕ Tambah User Access
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ERROR DARI CONTROLLER CATCH --}}
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-100 text-rose-700 px-4 py-3">
                    <div class="font-semibold mb-1">Gagal menyimpan data</div>
                    <div class="text-sm">{{ session('error') }}</div>
                </div>
            @endif

            {{-- SUCCESS --}}
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-100 text-emerald-700 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- VALIDATION ERROR --}}
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
                    menus: {{ old('menus') ? json_encode(old('menus')) : '[{menu_name: ``, module: ``, can_create: false, can_view: true, can_update: false, can_delete: false, can_approve: false}]' }},
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
                      action="{{ route('user-access-management.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    {{-- HEADER FORM --}}
                    <div class="bg-white p-6 rounded-2xl shadow">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800">Form User Access</h1>
                                <p class="text-sm text-slate-500">Input data akses user internal per sistem</p>
                            </div>

                            <a href="{{ route('user-access-management.index') }}"
                               class="inline-flex items-center justify-center bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-xl font-semibold shadow">
                                Kembali
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Nama User <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_user" value="{{ old('nama_user') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('nama_user') border-rose-400 @enderror">
                                @error('nama_user')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('email') border-rose-400 @enderror">
                                @error('email')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Role</label>
                                <input type="text" name="role" value="{{ old('role') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('role') border-rose-400 @enderror">
                                @error('role')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Divisi</label>
                                <input type="text" name="divisi" value="{{ old('divisi') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('divisi') border-rose-400 @enderror">
                                @error('divisi')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Cabang</label>
                                <input type="text" name="cabang" value="{{ old('cabang') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('cabang') border-rose-400 @enderror">
                                @error('cabang')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('penanggung_jawab') border-rose-400 @enderror">
                                @error('penanggung_jawab')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Status <span class="text-rose-500">*</span></label>
                                <select name="status"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('status') border-rose-400 @enderror">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="resign" {{ old('status') === 'resign' ? 'selected' : '' }}>Resign</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Tanggal Resign</label>
                                <input type="date" name="tgl_resign" value="{{ old('tgl_resign') }}"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('tgl_resign') border-rose-400 @enderror">
                                @error('tgl_resign')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Kategori System <span class="text-rose-500">*</span></label>
                                <select name="kategori_system"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('kategori_system') border-rose-400 @enderror">
                                    <option value="">Pilih System</option>
                                    @foreach (['SYOP','SERVER','ACCURATE','JPAYROLL','HELPDESK','CRS'] as $item)
                                        <option value="{{ $item }}" {{ old('kategori_system') === $item ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_system')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Workflow Status <span class="text-rose-500">*</span></label>
                                <select name="workflow_status"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('workflow_status') border-rose-400 @enderror">
                                    <option value="draft" {{ old('workflow_status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending_approval" {{ old('workflow_status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="approved" {{ old('workflow_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('workflow_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('workflow_status')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-7">
                                <input type="hidden" name="is_critical" value="0">
                                <input type="checkbox" name="is_critical" value="1"
                                    {{ old('is_critical') ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                <label class="text-sm font-medium text-slate-700">Critical Access</label>
                            </div>

                            <div class="flex items-center gap-3 pt-7">
                                <input type="hidden" name="approval_ceo" value="0">
                                <input type="checkbox" name="approval_ceo" value="1"
                                    {{ old('approval_ceo') ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                <label class="text-sm font-medium text-slate-700">Approval CEO</label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-600 mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="4"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('keterangan') border-rose-400 @enderror">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-600 mb-1">Lampiran</label>
                                <input type="file" name="lampiran"
                                    class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 @error('lampiran') border-rose-400 @enderror">
                                @error('lampiran')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- MENU AKSES --}}
                    <div class="bg-white p-6 rounded-2xl shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-800">Menu Akses</h2>
                                <p class="text-sm text-slate-500">Tambahkan satu atau lebih menu akses</p>
                            </div>

                            <button type="button" @click="addMenu"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-semibold shadow">
                                + Tambah Menu
                            </button>
                        </div>

                        @error('menus')
                            <p class="text-sm text-rose-600 mb-3">{{ $message }}</p>
                        @enderror

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
                                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama Menu <span class="text-rose-500">*</span></label>
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
                                               placeholder="contoh: master / transaksi / laporan"
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

                        {{-- DEBUG OLD MENUS --}}
                        @if (old('menus'))
                            <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4">
                                <div class="text-sm font-semibold text-slate-700 mb-2">Debug old menus:</div>
                                <pre class="text-xs text-slate-600 overflow-auto">{{ json_encode(old('menus'), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('user-access-management.index') }}"
                           class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-3 rounded-xl font-semibold">
                            Batal
                        </a>

                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-xl font-semibold shadow">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>