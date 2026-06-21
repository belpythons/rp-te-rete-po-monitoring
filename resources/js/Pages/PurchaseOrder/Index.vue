<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    procurements:    { type: Object, default: () => ({}) },
    totalPO:         { type: Number, default: 0 },
    availableMonths: { type: Array, default: () => [] },
    filters:         { type: Object, default: () => ({}) },
});

// Reactive Filter states
const searchQuery = ref(props.filters.search || '');
const activeMonth = ref(props.filters.month_year || '');

// Apply server-side filters
function applyFilters() {
    router.get('/purchase-order', {
        search: searchQuery.value || undefined,
        month_year: activeMonth.value || undefined,
    }, {
        preserveState: true,
        replace: true
    });
}

function triggerSearch() {
    applyFilters();
}

function handleMonthChange() {
    applyFilters();
}

function resetFilters() {
    searchQuery.value = '';
    activeMonth.value = '';
    router.get('/purchase-order');
}

// Extract paginated array
const filteredProcurements = computed(() => {
    if (Array.isArray(props.procurements)) {
        return props.procurements;
    }
    return props.procurements.data || [];
});

// Edit Modal state
const showEditModal = ref(false);
const editForm = useForm({
    id: '',
    no: '',
    rp_number: '',
    description: '',
    date_created: '',
    send_for_approval_general_director: '',
    buyer: '',
    te_in: '',
    te_out: '',
    re_te: '',
    po: '',
    vendor: '',
    delivery: '',
    so: '',
    qc: '',
    rr: '',
    status: 'Pending',
    phase: 'PO',
});

function openEditModal(item) {
    editForm.clearErrors();
    editForm.id = item.id;
    editForm.no = item.no || '';
    editForm.rp_number = item.rp_number || '';
    editForm.description = item.description || '';
    editForm.date_created = item.date_created || '';
    editForm.send_for_approval_general_director = item.send_for_approval_general_director || '';
    editForm.buyer = item.buyer || '';
    editForm.te_in = item.te_in || '';
    editForm.te_out = item.te_out || '';
    editForm.re_te = item.re_te || '';
    editForm.po = item.po || '';
    editForm.vendor = item.vendor || '';
    editForm.delivery = item.delivery || '';
    editForm.so = item.so || '';
    editForm.qc = item.qc || '';
    editForm.rr = item.rr || '';
    editForm.status = item.status || 'Pending';
    editForm.phase = item.phase || 'PO';

    showEditModal.value = true;
}

function submitUpdate() {
    editForm.put(`/procurement/update/${editForm.id}`, {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
}

// Delete Modal state
const showDeleteModal = ref(false);
const deleteId = ref(null);

function confirmDelete(id) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function submitDelete() {
    if (!deleteId.value) return;
    router.post(`/procurement/delete/${deleteId.value}`, {}, {
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteId.value = null;
        }
    });
}
</script>

<template>
    <AuthenticatedLayout title="Purchase Order (PO)">
        <!-- Single Focus Metric Card -->
        <section class="mb-8">
            <div class="max-w-xs rounded-xl p-5 text-white bg-pink-500 shadow-md flex flex-col justify-between h-28">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total PO</span>
                    <i class="bi bi-bag-check text-lg opacity-75"></i>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <span class="text-xs opacity-75">Purchase Order</span>
                    <span class="text-2xl font-bold font-mono">{{ totalPO }}</span>
                </div>
            </div>
        </section>

        <!-- Solid Neutral Table Wrapper Container -->
        <section class="bg-white border-2 border-black shadow-md overflow-hidden mb-8">
            <div class="p-6 border-b-2 border-black flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-6 bg-slate-800 rounded-full"></div>
                    <h3 class="text-lg font-bold text-slate-900">Data Monitoring Purchase Order</h3>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Export buttons for current phase -->
                    <div class="flex items-center gap-2">
                        <a 
                            :href="`/report/export/excel?phase=PO` + (activeMonth ? `&month_year=${activeMonth}` : '')" 
                            class="h-10 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold flex items-center justify-center gap-1 shadow-sm decoration-none cursor-pointer"
                        >
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a 
                            :href="`/report/export/pdf?phase=PO` + (activeMonth ? `&month_year=${activeMonth}` : '')" 
                            class="h-10 px-3 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold flex items-center justify-center gap-1 shadow-sm decoration-none cursor-pointer"
                        >
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>

                    <!-- Month & Year Filter Dropdown -->
                    <div class="flex items-center gap-2">
                        <select 
                            v-model="activeMonth" 
                            @change="handleMonthChange" 
                            class="h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white"
                        >
                            <option value="">Semua Bulan</option>
                            <option v-for="m in availableMonths" :key="m.value" :value="m.value">
                                {{ m.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Search Input & Cari Button -->
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keyup.enter="triggerSearch"
                            placeholder="Cari..." 
                            class="w-40 h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" 
                        />
                        <button 
                            @click="triggerSearch"
                            class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition"
                        >
                            Cari
                        </button>
                    </div>

                    <!-- Reset Filters Button -->
                    <button 
                        v-if="searchQuery || activeMonth"
                        @click="resetFilters"
                        class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Data Table with base font size -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white border-b-2 border-black whitespace-nowrap">
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-16">No</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-40">Kode (RP)</th>
                            <th class="p-4 text-base font-bold uppercase tracking-wider min-w-[320px]">Deskripsi Barang</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">Date Created</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">Send Gen Dir</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-28">Buyer</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">TE In</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">TE Out</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">Re-TE</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">PO</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-48">Vendor</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">Delivery</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">SO</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">QC</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">RR</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-36">Status</th>
                            <th class="p-4 text-center text-base font-bold uppercase tracking-wider w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-900 text-base">
                        <tr v-for="item in filteredProcurements" :key="item.id" class="hover:bg-slate-50/50 transition whitespace-nowrap">
                            <td class="p-4 text-center font-mono text-base">{{ item.no }}</td>
                            <td class="p-4 text-center font-mono font-semibold text-slate-900 select-all text-base">{{ item.rp_number }}</td>
                            <td class="p-4 font-medium text-base whitespace-normal min-w-[320px]">{{ item.description }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.date_created || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.send_for_approval_general_director || '—' }}</td>
                            <td class="p-4 text-center text-base text-slate-500">{{ item.buyer || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.te_in || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.te_out || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.re_te || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.po || '—' }}</td>
                            <td class="p-4 text-center text-base text-slate-500">{{ item.vendor || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.delivery || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.so || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.qc || '—' }}</td>
                            <td class="p-4 text-center font-mono text-base text-slate-500">{{ item.rr || '—' }}</td>
                            <td class="p-4 text-center text-base">
                                <span v-if="item.status === 'Disetujui'" class="inline-block bg-green-50 text-green-700 border border-green-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Disetujui</span>
                                <span v-else-if="item.status === 'Tidak Disetujui'" class="inline-block bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Tidak Disetujui</span>
                                <span v-else-if="item.status === 'Pending'" class="inline-block bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Pending</span>
                                <span v-else class="inline-block bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">{{ item.status }}</span>
                            </td>
                            <td class="p-4 text-base">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        @click="openEditModal(item)" 
                                        class="h-8 px-3 bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-semibold text-xs rounded-lg transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button 
                                        @click="confirmDelete(item.id)" 
                                        class="h-8 px-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-xs rounded-lg transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredProcurements.length === 0">
                            <td colspan="17" class="p-8 text-center text-slate-400 font-mono text-base">
                                Data pengadaan kosong / tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Server-Side Pagination Controls -->
            <div v-if="procurements.links && procurements.links.length > 3" class="px-5 py-4 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-500 font-mono">
                    Menampilkan halaman {{ procurements.current_page }} dari {{ procurements.last_page }} (Total: {{ procurements.total }} data)
                </div>
                <div class="flex items-center gap-1 flex-wrap">
                    <a
                        v-for="(link, index) in procurements.links"
                        :key="index"
                        :href="link.url || '#'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-semibold font-mono transition duration-150 decoration-none"
                        :class="[
                            link.active 
                                ? 'bg-blue-600 border-blue-600 text-white shadow-sm' 
                                : link.url 
                                    ? 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' 
                                    : 'bg-white border-slate-100 text-slate-300 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    ></a>
                </div>
            </div>
        </section>

        <!-- MODAL EDIT -->
        <Transition name="fade">
            <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl border border-slate-100 w-full max-w-[800px] max-h-[90vh] overflow-hidden shadow-2xl flex flex-col relative">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">Edit Data Purchase Order</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                    </div>

                    <form @submit.prevent="submitUpdate" class="p-6 overflow-y-auto space-y-6 flex-grow text-left">
                        <div v-if="Object.keys(editForm.errors).length > 0" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-xs space-y-1 font-semibold">
                            <div v-for="(err, key) in editForm.errors" :key="key">
                                • {{ err }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Phase Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fase (Phase)</label>
                                <select v-model="editForm.phase" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white">
                                    <option value="RP">Request Purchasing (RP)</option>
                                    <option value="TE">Technical Evaluation (TE)</option>
                                    <option value="RE-TE">Re-Technical Evaluation (RE-TE)</option>
                                    <option value="PO">Purchase Order (PO)</option>
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Persetujuan</label>
                                <select v-model="editForm.status" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white">
                                    <option value="Pending">Pending</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Tidak Disetujui">Tidak Disetujui</option>
                                    <option value="RP">RP</option>
                                    <option value="TE">TE</option>
                                    <option value="RE-TE">RE-TE</option>
                                    <option value="PO">PO</option>
                                </select>
                            </div>

                            <!-- No -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No (Serial)</label>
                                <input type="text" v-model="editForm.no" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- RP Number -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Pengadaan (RP Number)</label>
                                <input type="text" v-model="editForm.rp_number" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm font-mono bg-white" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description</label>
                                <input type="text" v-model="editForm.description" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- Date Created -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date Created</label>
                                <input type="text" v-model="editForm.date_created" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- Send for Approval General Director -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Send Gen Dir Approval</label>
                                <input type="text" v-model="editForm.send_for_approval_general_director" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- Buyer -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Buyer (Inisial)</label>
                                <input type="text" v-model="editForm.buyer" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- PO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">PO Date/Number</label>
                                <input type="text" v-model="editForm.po" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- SO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SO Date/Number</label>
                                <input type="text" v-model="editForm.so" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- QC -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">QC Date/Result</label>
                                <input type="text" v-model="editForm.qc" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- Delivery -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Delivery Date</label>
                                <input type="text" v-model="editForm.delivery" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- RR -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RR Date</label>
                                <input type="text" v-model="editForm.rr" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>

                            <!-- Vendor -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Vendor Name</label>
                                <input type="text" v-model="editForm.vendor" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white" />
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100">
                            <button 
                                type="submit" 
                                :disabled="editForm.processing" 
                                class="w-full sm:flex-grow h-11 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition flex items-center justify-center gap-2 cursor-pointer shadow-md"
                            >
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <button 
                                type="button" 
                                @click="showEditModal = false" 
                                class="w-full sm:w-32 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition cursor-pointer"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- DELETE CONFIRM MODAL -->
        <Transition name="fade">
            <div v-if="showDeleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl border border-slate-100 w-full max-w-[400px] p-6 shadow-2xl text-center">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Hapus Data Ini?</h3>
                    <p class="text-xs text-slate-500 mb-6">Tindakan ini tidak dapat dibatalkan. Data pengadaan akan dihapus permanen.</p>
                    <div class="flex gap-3 justify-center">
                        <button 
                            @click="showDeleteModal = false" 
                            class="h-10 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            @click="submitDelete" 
                            class="h-10 px-5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition shadow-md cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
