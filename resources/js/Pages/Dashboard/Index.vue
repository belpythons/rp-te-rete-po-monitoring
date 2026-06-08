<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    procurements: { type: Array, default: () => [] },
    totalRP:      { type: Number, default: 0 },
    totalTE:      { type: Number, default: 0 },
    totalRETE:    { type: Number, default: 0 },
    totalPO:      { type: Number, default: 0 },
    filters:      { type: Object, default: () => ({}) },
});

// Calculate total of all data
const totalAll = computed(() => props.totalRP + props.totalTE + props.totalRETE + props.totalPO);

// Reactive Filter states
const searchQuery = ref(props.filters.search || '');
const activeSearch = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || null);

// Filter trigger
function triggerSearch() {
    activeSearch.value = searchQuery.value;
}

// Client-side filtering
const filteredProcurements = computed(() => {
    let data = props.procurements;
    if (statusFilter.value) {
        data = data.filter(p => p.status === statusFilter.value);
    }
    if (activeSearch.value) {
        const q = activeSearch.value.toLowerCase();
        data = data.filter(p =>
            (p.rp_number && p.rp_number.toLowerCase().includes(q)) ||
            (p.description && p.description.toLowerCase().includes(q)) ||
            (p.vendor && p.vendor.toLowerCase().includes(q)) ||
            (p.status && p.status.toLowerCase().includes(q))
        );
    }
    return data;
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
    status: 'RP',
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
    editForm.status = item.status || 'RP';

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

// Toggle status filter from cards
function toggleStatusFilter(status) {
    if (statusFilter.value === status) {
        statusFilter.value = null;
    } else {
        statusFilter.value = status;
    }
}
</script>

<template>
    <AuthenticatedLayout title="Dashboard Monitoring">
        <!-- Stats Cards: 5 rounded-lg cards with light shadow-md -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Card Semua Data -->
            <div 
                @click="statusFilter = null"
                class="rounded-xl p-5 cursor-pointer flex flex-col justify-between h-28 transition-all duration-150 shadow-md hover:shadow-lg text-white"
                :class="statusFilter === null ? 'bg-slate-900 ring-4 ring-slate-400' : 'bg-slate-800'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold uppercase tracking-wider">Semua Data</span>
                    <i class="bi bi-folder-fill text-lg opacity-75"></i>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <span class="text-xs opacity-75">Total Item</span>
                    <span class="text-2xl font-bold font-mono">{{ totalAll }}</span>
                </div>
            </div>

            <!-- Card RP -->
            <div 
                @click="toggleStatusFilter('RP')"
                class="rounded-xl p-5 cursor-pointer flex flex-col justify-between h-28 transition-all duration-150 shadow-md hover:shadow-lg text-white"
                :class="statusFilter === 'RP' ? 'bg-blue-700 ring-4 ring-blue-300' : 'bg-blue-600'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total RP</span>
                    <i class="bi bi-file-earmark-text text-lg opacity-75"></i>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <span class="text-xs opacity-75">Req Purchasing</span>
                    <span class="text-2xl font-bold font-mono">{{ totalRP }}</span>
                </div>
            </div>

            <!-- Card TE -->
            <div 
                @click="toggleStatusFilter('TE')"
                class="rounded-xl p-5 cursor-pointer flex flex-col justify-between h-28 transition-all duration-150 shadow-md hover:shadow-lg text-white"
                :class="statusFilter === 'TE' ? 'bg-green-600 ring-4 ring-green-300' : 'bg-green-500'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total TE</span>
                    <i class="bi bi-clipboard-check text-lg opacity-75"></i>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <span class="text-xs opacity-75">Tech Evaluation</span>
                    <span class="text-2xl font-bold font-mono">{{ totalTE }}</span>
                </div>
            </div>

            <!-- Card RE-TE -->
            <div 
                @click="toggleStatusFilter('RE-TE')"
                class="rounded-xl p-5 cursor-pointer flex flex-col justify-between h-28 transition-all duration-150 shadow-md hover:shadow-lg text-white"
                :class="statusFilter === 'RE-TE' ? 'bg-orange-500 ring-4 ring-orange-300' : 'bg-orange-400'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total Re-TE</span>
                    <i class="bi bi-arrow-repeat text-lg opacity-75"></i>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <span class="text-xs opacity-75">Re-Tech Eval</span>
                    <span class="text-2xl font-bold font-mono">{{ totalRETE }}</span>
                </div>
            </div>

            <!-- Card PO -->
            <div 
                @click="toggleStatusFilter('PO')"
                class="rounded-xl p-5 cursor-pointer flex flex-col justify-between h-28 transition-all duration-150 shadow-md hover:shadow-lg text-white"
                :class="statusFilter === 'PO' ? 'bg-pink-600 ring-4 ring-pink-300' : 'bg-pink-500'"
            >
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

        <!-- Controls (Search & Add Button) -->
        <section class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-6 bg-blue-600 rounded-full"></div>
                    <h3 class="text-base font-bold text-slate-800">Daftar Procurement</h3>
                    <span v-if="statusFilter" class="bg-blue-50 text-blue-700 text-xs px-2.5 py-0.5 rounded-full font-semibold border border-blue-100 uppercase">
                        Filter: {{ statusFilter }}
                    </span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input & Cari Button -->
                    <div class="flex w-full sm:w-auto gap-2">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keyup.enter="triggerSearch"
                            placeholder="Cari kode / deskripsi..." 
                            class="w-full sm:w-60 h-10 px-4 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" 
                        />
                        <button 
                            @click="triggerSearch"
                            class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition"
                        >
                            Cari
                        </button>
                    </div>

                    <!-- Clear filter icon -->
                    <button 
                        v-if="statusFilter || searchQuery || activeSearch"
                        @click="statusFilter = null; searchQuery = ''; activeSearch = '';"
                        class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition"
                        title="Reset Filter"
                    >
                        Reset
                    </button>

                    <!-- Add Button -->
                    <a 
                        href="/procurement/create" 
                        class="h-10 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg flex items-center justify-center gap-2 transition text-sm whitespace-nowrap decoration-none"
                    >
                        <i class="bi bi-plus-lg"></i> Tambah Pengadaan
                    </a>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white border-b border-slate-700">
                            <th class="p-4 text-center text-xs font-semibold uppercase tracking-wider w-16">No</th>
                            <th class="p-4 text-center text-xs font-semibold uppercase tracking-wider w-36">Kode (RP)</th>
                            <th class="p-4 text-xs font-semibold uppercase tracking-wider">Deskripsi Barang</th>
                            <th class="p-4 text-center text-xs font-semibold uppercase tracking-wider w-28">Status</th>
                            <th class="p-4 text-center text-xs font-semibold uppercase tracking-wider w-40">Tanggal Created</th>
                            <th class="p-4 text-center text-xs font-semibold uppercase tracking-wider w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                        <tr v-for="item in filteredProcurements" :key="item.id" class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-center font-mono text-xs">{{ item.no }}</td>
                            <td class="p-4 text-center font-mono font-semibold text-slate-900 select-all">{{ item.rp_number }}</td>
                            <td class="p-4 font-medium">{{ item.description }}</td>
                            <td class="p-4 text-center">
                                <span v-if="item.status === 'RP'" class="inline-block bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">RP</span>
                                <span v-else-if="item.status === 'TE'" class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">TE</span>
                                <span v-else-if="item.status === 'RE-TE'" class="inline-block bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">RE-TE</span>
                                <span v-else-if="item.status === 'PO'" class="inline-block bg-pink-50 text-pink-700 border border-pink-200 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">PO</span>
                            </td>
                            <td class="p-4 text-center font-mono text-xs text-slate-500">{{ item.date_created || '—' }}</td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button 
                                        @click="openEditModal(item)" 
                                        class="h-8 px-3 bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-semibold text-xs rounded-lg transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
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
                            <td colspan="6" class="p-8 text-center text-slate-400 font-mono text-sm">
                                Data pengadaan kosong / tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MODAL EDIT (COMPREHENSIVE FOR ALL 15 COLUMNS) -->
        <Transition name="fade">
            <div v-if="showEditModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl border border-slate-100 w-full max-w-[800px] max-h-[90vh] overflow-hidden shadow-2xl flex flex-col relative">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="text-lg font-bold text-slate-800">Edit Data Procurement</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                    </div>

                    <form @submit.prevent="submitUpdate" class="p-6 overflow-y-auto space-y-6 flex-grow text-left">
                        <!-- Global Validation Errors -->
                        <div v-if="Object.keys(editForm.errors).length > 0" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-xs space-y-1 font-semibold">
                            <div v-for="(err, key) in editForm.errors" :key="key">
                                • {{ err }}
                            </div>
                        </div>

                        <!-- 2 Column Grid for Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Status Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                                <select v-model="editForm.status" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                                    <option value="RP">Request Purchasing (RP)</option>
                                    <option value="TE">Technical Evaluation (TE)</option>
                                    <option value="RE-TE">Re-Technical Evaluation (RE-TE)</option>
                                    <option value="PO">Purchase Order (PO)</option>
                                </select>
                            </div>

                            <!-- No -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No (Serial)</label>
                                <input type="text" v-model="editForm.no" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- RP Number -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Pengadaan (RP Number)</label>
                                <input type="text" v-model="editForm.rp_number" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm font-mono" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description</label>
                                <input type="text" v-model="editForm.description" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Date Created -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date Created</label>
                                <input type="text" v-model="editForm.date_created" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Send for Approval General Director -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Send Gen Dir Approval</label>
                                <input type="text" v-model="editForm.send_for_approval_general_director" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Buyer -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Buyer (Inisial)</label>
                                <input type="text" v-model="editForm.buyer" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- TE In -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">TE In Date</label>
                                <input type="text" v-model="editForm.te_in" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- TE Out -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">TE Out Date</label>
                                <input type="text" v-model="editForm.te_out" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- RE-TE -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RE-TE Date</label>
                                <input type="text" v-model="editForm.re_te" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- PO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">PO Date/Number</label>
                                <input type="text" v-model="editForm.po" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- SO -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SO Date/Number</label>
                                <input type="text" v-model="editForm.so" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- QC -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">QC Date/Result</label>
                                <input type="text" v-model="editForm.qc" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Delivery -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Delivery Date</label>
                                <input type="text" v-model="editForm.delivery" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- RR -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RR Date</label>
                                <input type="text" v-model="editForm.rr" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Vendor -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Vendor Name</label>
                                <input type="text" v-model="editForm.vendor" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
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
