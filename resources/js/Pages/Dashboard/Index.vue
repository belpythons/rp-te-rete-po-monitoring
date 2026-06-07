<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    procurements: { type: Array, default: () => [] },
    totalRP:      { type: Number, default: 0 },
    totalTE:      { type: Number, default: 0 },
    totalRETE:    { type: Number, default: 0 },
    totalPO:      { type: Number, default: 0 },
    filters:      { type: Object, default: () => ({}) },
});

const page = usePage();

// Reactive Filter states
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || null);

// Dynamic filtering on client-side for ultra-fast response
const filteredProcurements = computed(() => {
    let data = props.procurements;
    if (statusFilter.value) {
        data = data.filter(p => p.status === statusFilter.value);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(p =>
            p.kode_pengadaan.toLowerCase().includes(q) ||
            (p.nama_barang && p.nama_barang.toLowerCase().includes(q)) ||
            (p.vendor && p.vendor.toLowerCase().includes(q)) ||
            p.status.toLowerCase().includes(q)
        );
    }
    return data;
});

// Toast / Flash handling
const showToast = ref(!!page.props.flash?.success);
const toastMessage = computed(() => page.props.flash?.success || '');
watch(() => page.props.flash?.success, (newVal) => {
    if (newVal) {
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
});

// Modal state
const showEditModal = ref(false);
const editForm = useForm({
    id: '',
    status: 'RP',
    kode_pengadaan: '',
    nama_barang: '',
    vendor: '',
    quantity: '',
    departemen: '',
    keterangan: '',
    hasil_evaluasi: '',
    catatan: '',
    tanggal: '',
});

// Helper for caching dates for Edit Modal
const datesCache = ref({ RP: '', TE: '', RETE: '', PO: '' });

function openEditModal(item) {
    editForm.clearErrors();
    editForm.id = item.id;
    editForm.status = item.status;
    editForm.kode_pengadaan = item.kode_pengadaan;
    editForm.nama_barang = item.nama_barang || '';
    editForm.vendor = item.vendor || '';
    editForm.quantity = item.quantity || '';
    editForm.departemen = item.departemen || '';
    editForm.keterangan = item.keterangan || '';
    editForm.hasil_evaluasi = item.hasil_evaluasi || '';
    editForm.catatan = item.catatan || '';

    datesCache.value.RP = item.tanggal_in ? item.tanggal_in.split('T')[0] : '';
    datesCache.value.TE = item.tanggal_te ? item.tanggal_te.split('T')[0] : '';
    datesCache.value.RETE = item.tanggal_rete ? item.tanggal_rete.split('T')[0] : '';
    datesCache.value.PO = item.tanggal_po ? item.tanggal_po.split('T')[0] : '';

    updateModalDate(item.status);
    showEditModal.value = true;
}

function updateModalDate(status) {
    if (status === 'RP') editForm.tanggal = datesCache.value.RP || new Date().toISOString().split('T')[0];
    else if (status === 'TE') editForm.tanggal = datesCache.value.TE || new Date().toISOString().split('T')[0];
    else if (status === 'RE-TE') editForm.tanggal = datesCache.value.RETE || new Date().toISOString().split('T')[0];
    else if (status === 'PO') editForm.tanggal = datesCache.value.PO || new Date().toISOString().split('T')[0];
}

watch(() => editForm.status, (newStatus) => {
    updateModalDate(newStatus);
});

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

// Phase automation approvals/rejects
function approvePhase(id) {
    router.post(`/procurement/approve-phase/${id}`, {}, {
        preserveScroll: true
    });
}

function approvePhaseWithTarget(id, target) {
    router.post(`/procurement/approve-phase/${id}`, { target }, {
        preserveScroll: true
    });
}

function toggleStatusFilter(status) {
    if (statusFilter.value === status) {
        statusFilter.value = null;
    } else {
        statusFilter.value = status;
    }
}

// Helper formatting tanggal
function formatTanggal(item) {
    const d = item.tanggal_in || item.tanggal_te || item.tanggal_rete || item.tanggal_po;
    if (!d) return '-';
    return d.split('T')[0];
}
</script>

<template>
    <AuthenticatedLayout title="Selamat Datang, Admin">
        <!-- Toast Success -->
        <Transition name="slide">
            <div v-if="showToast" class="fixed top-5 left-1/2 -translate-x-1/2 bg-white border-4 border-black p-4 shadow-[6px_6px_0px_0px_#000] flex items-center gap-3 z-[9999]">
                <div class="w-6 h-6 border-2 border-black bg-[#4ADE80] flex items-center justify-center">
                    <i class="bi bi-check-lg text-sm font-black"></i>
                </div>
                <div class="flex-1 text-xs font-black uppercase tracking-wider text-black">{{ toastMessage }}</div>
                <div class="text-xl cursor-pointer font-black text-black hover:text-red-500" @click="showToast = false">×</div>
            </div>
        </Transition>

        <!-- Stats Cards: Exactly 4 per status code -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card RP -->
            <div 
                @click="toggleStatusFilter('RP')"
                class="border-4 border-black p-5 cursor-pointer flex flex-col justify-between h-32 transition-all duration-150 shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0px_0px_#000]"
                :class="statusFilter === 'RP' ? 'bg-[#FACC15]' : 'bg-white'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider bg-black text-white px-2 py-0.5 border border-black">REQUEST PURCHASING</span>
                    <i class="bi bi-file-earmark-text text-xl"></i>
                </div>
                <div class="flex justify-between items-baseline mt-4">
                    <span class="text-xs font-bold text-gray-700">Total RP</span>
                    <span class="text-3xl font-black font-mono">{{ totalRP }}</span>
                </div>
            </div>

            <!-- Card TE -->
            <div 
                @click="toggleStatusFilter('TE')"
                class="border-4 border-black p-5 cursor-pointer flex flex-col justify-between h-32 transition-all duration-150 shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0px_0px_#000]"
                :class="statusFilter === 'TE' ? 'bg-[#22D3EE]' : 'bg-white'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider bg-black text-white px-2 py-0.5 border border-black">TECHNICAL EVALUATION</span>
                    <i class="bi bi-clipboard-check text-xl"></i>
                </div>
                <div class="flex justify-between items-baseline mt-4">
                    <span class="text-xs font-bold text-gray-700">Total TE</span>
                    <span class="text-3xl font-black font-mono">{{ totalTE }}</span>
                </div>
            </div>

            <!-- Card RE-TE -->
            <div 
                @click="toggleStatusFilter('RE-TE')"
                class="border-4 border-black p-5 cursor-pointer flex flex-col justify-between h-32 transition-all duration-150 shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0px_0px_#000]"
                :class="statusFilter === 'RE-TE' ? 'bg-[#FF80FF]' : 'bg-white'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider bg-black text-white px-2 py-0.5 border border-black">RE-TE EVALUATION</span>
                    <i class="bi bi-arrow-repeat text-xl"></i>
                </div>
                <div class="flex justify-between items-baseline mt-4">
                    <span class="text-xs font-bold text-gray-700">Total Re-TE</span>
                    <span class="text-3xl font-black font-mono">{{ totalRETE }}</span>
                </div>
            </div>

            <!-- Card PO -->
            <div 
                @click="toggleStatusFilter('PO')"
                class="border-4 border-black p-5 cursor-pointer flex flex-col justify-between h-32 transition-all duration-150 shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0px_0px_#000]"
                :class="statusFilter === 'PO' ? 'bg-[#4ADE80]' : 'bg-white'"
            >
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider bg-black text-white px-2 py-0.5 border border-black">PURCHASE ORDER</span>
                    <i class="bi bi-bag-check text-xl"></i>
                </div>
                <div class="flex justify-between items-baseline mt-4">
                    <span class="text-xs font-bold text-gray-700">Total PO</span>
                    <span class="text-3xl font-black font-mono">{{ totalPO }}</span>
                </div>
            </div>
        </section>

        <!-- Bilah Kontrol (Search & Actions) -->
        <section class="bg-white border-4 border-black p-5 shadow-[6px_6px_0px_0px_#000] mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-black border border-black"></div>
                    <h3 class="text-lg font-black uppercase tracking-wider text-black">Filter & Aksi</h3>
                </div>
                <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            placeholder="Cari kode / barang..." 
                            class="w-full h-11 px-4 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" 
                        />
                    </div>

                    <!-- Clear Filter Button (Visible only when statusFilter is active) -->
                    <button 
                        v-if="statusFilter"
                        @click="statusFilter = null"
                        class="h-11 px-4 border-2 border-black bg-white font-bold text-xs uppercase hover:bg-gray-100 transition-all"
                    >
                        Reset Filter
                    </button>

                    <!-- Add Button -->
                    <Link 
                        href="/procurement/create" 
                        class="h-11 px-5 bg-[#4ADE80] text-black font-black border-4 border-black flex items-center justify-center gap-2 shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all decoration-none text-xs uppercase"
                    >
                        <i class="bi bi-plus-lg font-bold"></i> Tambah Pengadaan
                    </Link>
                </div>
            </div>
        </section>

        <!-- Grid Data Table -->
        <section class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_#000] overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-black text-white font-black border-b-4 border-black">
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-16">No</th>
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Kode Pengadaan</th>
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Nama Barang</th>
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Vendor</th>
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Status</th>
                            <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Tanggal</th>
                            <th class="p-4 text-center text-xs font-black uppercase w-[380px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-black font-bold text-sm">
                        <tr v-for="(item, index) in filteredProcurements" :key="item.id" class="hover:bg-[#F4F4F0] transition duration-100">
                            <td class="p-4 border-r-2 border-black text-center font-mono">{{ index + 1 }}</td>
                            <td class="p-4 border-r-2 border-black text-center font-mono font-black text-black select-all">{{ item.kode_pengadaan }}</td>
                            <td class="p-4 border-r-2 border-black">{{ item.nama_barang }}</td>
                            <td class="p-4 border-r-2 border-black">{{ item.vendor || '—' }}</td>
                            <td class="p-4 border-r-2 border-black text-center">
                                <span v-if="item.status === 'RP'" class="inline-block border-2 border-black bg-[#FACC15] text-black px-2 py-0.5 text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]">RP</span>
                                <span v-else-if="item.status === 'TE'" class="inline-block border-2 border-black bg-[#22D3EE] text-black px-2 py-0.5 text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]">TE</span>
                                <span v-else-if="item.status === 'RE-TE'" class="inline-block border-2 border-black bg-[#FF80FF] text-black px-2 py-0.5 text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]">RE-TE</span>
                                <span v-else-if="item.status === 'PO'" class="inline-block border-2 border-black bg-[#4ADE80] text-black px-2 py-0.5 text-xs font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]">PO</span>
                            </td>
                            <td class="p-4 border-r-2 border-black text-center font-mono text-xs">{{ formatTanggal(item) }}</td>
                            <td class="p-4 flex items-center justify-center gap-2 flex-wrap text-center">
                                <!-- Edit Button -->
                                <button 
                                    @click="openEditModal(item)" 
                                    class="h-8 px-3 border-2 border-black bg-[#FACC15] text-black font-bold text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all flex items-center gap-1 cursor-pointer"
                                >
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                <!-- Delete Button -->
                                <button 
                                    @click="confirmDelete(item.id)" 
                                    class="h-8 px-3 border-2 border-black bg-[#FF80FF] text-black font-bold text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all flex items-center gap-1 cursor-pointer"
                                >
                                    <i class="bi bi-trash"></i> Hapus
                                </button>

                                <!-- Workflow state transitions -->
                                <!-- Jika status RP -> Tampilkan Setujui TE -->
                                <button 
                                    v-if="item.status === 'RP'" 
                                    @click="approvePhase(item.id)" 
                                    class="h-8 px-3 border-2 border-black bg-[#4ADE80] text-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all cursor-pointer"
                                >
                                    Setujui TE ➡️
                                </button>

                                <!-- Jika status TE -> Tampilkan Rilis PO dan Refuse / Re-Eval -->
                                <template v-else-if="item.status === 'TE'">
                                    <button 
                                        @click="approvePhaseWithTarget(item.id, 'PO')" 
                                        class="h-8 px-3 border-2 border-black bg-[#4ADE80] text-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all cursor-pointer"
                                    >
                                        Rilis PO 🎯
                                    </button>
                                    <button 
                                        @click="approvePhaseWithTarget(item.id, 'RE-TE')" 
                                        class="h-8 px-3 border-2 border-black bg-[#FF80FF] text-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all cursor-pointer"
                                    >
                                        Re-Eval 🔄
                                    </button>
                                </template>

                                <!-- Jika status RE-TE -> Tampilkan Rilis PO -->
                                <button 
                                    v-else-if="item.status === 'RE-TE'" 
                                    @click="approvePhase(item.id)" 
                                    class="h-8 px-3 border-2 border-black bg-[#4ADE80] text-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] active:shadow-none transition-all cursor-pointer"
                                >
                                    Rilis PO 🎯
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredProcurements.length === 0">
                            <td colspan="7" class="p-8 text-center text-gray-500 font-mono">
                                Data pengadaan kosong.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MODAL EDIT (VUE 3 REACTIVE) -->
        <Transition name="fade">
            <div v-if="showEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[999] flex items-center justify-center p-4">
                <div class="bg-white border-4 border-black w-full max-w-[540px] max-h-[90vh] overflow-y-auto p-6 shadow-[8px_8px_0px_0px_#000] relative">
                    <h3 class="text-xl font-black uppercase tracking-wider text-black border-b-2 border-black pb-3 mb-4">Edit Data Procurement</h3>

                    <!-- Global Validation Errors -->
                    <div v-if="Object.keys(editForm.errors).length > 0" class="mb-4 bg-[#FF80FF]/25 border-2 border-black p-3 text-xs space-y-1 font-bold">
                        <div v-for="(err, key) in editForm.errors" :key="key">
                            · {{ err }}
                        </div>
                    </div>

                    <form @submit.prevent="submitUpdate" class="space-y-4 text-left">
                        <!-- Status Dropdown -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Status</label>
                            <select v-model="editForm.status" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm">
                                <option value="RP">Request Purchasing (RP)</option>
                                <option value="TE">Technical Evaluation (TE)</option>
                                <option value="RE-TE">Re-Technical Evaluation (RE-TE)</option>
                                <option value="PO">Purchase Order (PO)</option>
                            </select>
                        </div>

                        <!-- Kode Pengadaan -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Kode Pengadaan</label>
                            <input type="text" v-model="editForm.kode_pengadaan" required class="w-full h-11 px-3 border-2 border-black font-mono focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <!-- Dynamic Fields with v-if matching creation rules -->
                        <div v-if="['RP', 'RE-TE', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Nama Barang</label>
                            <input type="text" v-model="editForm.nama_barang" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <div v-if="['TE', 'RE-TE', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Vendor</label>
                            <input type="text" v-model="editForm.vendor" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <div v-if="['RP', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Quantity</label>
                            <input type="text" v-model="editForm.quantity" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <div v-if="['RP', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Departemen</label>
                            <input type="text" v-model="editForm.departemen" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <div v-if="['RP', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Keterangan</label>
                            <textarea v-model="editForm.keterangan" rows="2" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y"></textarea>
                        </div>

                        <div v-if="['TE', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Hasil Evaluasi</label>
                            <textarea v-model="editForm.hasil_evaluasi" rows="2" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y"></textarea>
                        </div>

                        <div v-if="['RE-TE', 'PO'].includes(editForm.status)">
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Catatan</label>
                            <textarea v-model="editForm.catatan" rows="2" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Tanggal</label>
                            <input type="date" v-model="editForm.tanggal" class="w-full h-11 px-3 border-2 border-black font-mono focus:ring-0 focus:outline-none bg-white text-sm" />
                        </div>

                        <div class="pt-4 flex flex-col gap-3">
                            <button 
                                type="submit" 
                                :disabled="editForm.processing" 
                                class="w-full h-12 bg-[#4ADE80] text-black font-black border-4 border-black uppercase tracking-wider text-xs shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all cursor-pointer flex items-center justify-center gap-2"
                            >
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <button 
                                type="button" 
                                @click="showEditModal = false" 
                                class="w-full h-12 bg-white text-black font-bold border-2 border-black uppercase tracking-wider text-xs hover:bg-gray-100 transition-all cursor-pointer"
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
            <div v-if="showDeleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[9999] flex items-center justify-center p-4">
                <div class="bg-white border-4 border-black w-full max-w-[400px] p-6 shadow-[8px_8px_0px_0px_#000] text-center">
                    <div class="w-12 h-12 border-4 border-[#FF80FF] bg-[#FF80FF] text-black rounded-none flex items-center justify-center text-xl font-black mx-auto mb-4">!</div>
                    <h3 class="text-md font-black uppercase tracking-wider text-black mb-2">Hapus Data Ini?</h3>
                    <p class="text-xs font-mono font-bold text-gray-600 mb-6">Data yang dihapus tidak dapat dikembalikan.</p>
                    <div class="flex gap-3 justify-center">
                        <button 
                            @click="showDeleteModal = false" 
                            class="h-10 px-6 border-2 border-black bg-white font-bold text-xs uppercase hover:bg-gray-100 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            @click="submitDelete" 
                            class="h-10 px-6 border-2 border-black bg-[#FF80FF] text-black font-black text-xs uppercase hover:bg-[#FF80FF]/90 shadow-[2px_2px_0px_0px_#000] active:shadow-none hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all cursor-pointer"
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
.slide-enter-active, .slide-leave-active {
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.slide-enter-from {
    transform: translate(-50%, -20px);
    opacity: 0;
}
.slide-leave-to {
    transform: translate(-50%, -10px);
    opacity: 0;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
