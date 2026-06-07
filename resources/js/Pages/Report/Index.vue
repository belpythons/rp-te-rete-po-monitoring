<script setup>
import { ref, computed, onUnmounted, getCurrentInstance } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    procurements: { type: Array, default: () => [] },
    importLogs:   { type: Array, default: () => [] },
    stats:        { type: Object, default: () => ({}) },
});

const page = usePage();
const echoInstance = getCurrentInstance()?.appContext.config.globalProperties.$echo;

const isImporting   = ref(false);
const importLogId   = ref(null);
const progressPct   = ref(0);
const progressRows  = ref(0);
const progressTotal = ref(0);
const importResult  = ref(null);
const dragOver      = ref(false);
const importForm    = useForm({ file: null });
const fileInputRef  = ref(null);
const selectedFileName = ref('');
const searchQuery   = ref('');

const filteredProcurements = computed(() => {
    if (!searchQuery.value) return props.procurements;
    const q = searchQuery.value.toLowerCase();
    return props.procurements.filter(p =>
        p.kode_pengadaan.toLowerCase().includes(q) ||
        (p.nama_barang && p.nama_barang.toLowerCase().includes(q)) ||
        (p.vendor && p.vendor.toLowerCase().includes(q)) ||
        p.status.toLowerCase().includes(q)
    );
});

function onFileSelect(event) {
    const file = event.target.files?.[0];
    if (file) { 
        importForm.file = file; 
        selectedFileName.value = file.name; 
    }
}

function onDrop(event) {
    dragOver.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) { 
        importForm.file = file; 
        selectedFileName.value = file.name; 
    }
}

function clearFile() {
    importForm.file = null; 
    selectedFileName.value = '';
    if (fileInputRef.value) fileInputRef.value.value = '';
}

function submitImport() {
    if (!importForm.file) return;
    importForm.post('/report/import', {
        forceFormData: true, 
        preserveScroll: true,
        onSuccess: () => {
            const flash = page.props.flash;
            if (flash?.import_log_id) {
                importLogId.value = flash.import_log_id;
                isImporting.value = true; 
                progressPct.value = 0; 
                importResult.value = null;
                listenToImportChannel(flash.import_log_id);
            }
            clearFile();
        },
    });
}

let echoChannel = null;
function listenToImportChannel(logId) {
    if (!echoInstance) { 
        pollImportStatus(logId); 
        return; 
    }
    echoChannel = echoInstance.private(`import.${logId}`)
        .listen('.import.progress', (data) => {
            progressPct.value = data.percentage; 
            progressRows.value = data.processed_rows; 
            progressTotal.value = data.total_rows;
        })
        .listen('.import.completed', (data) => {
            progressPct.value = 100; 
            isImporting.value = false;
            importResult.value = { 
                success_count: data.success_count, 
                failure_count: data.failure_count, 
                error_file_url: data.error_file_url 
            };
            setTimeout(() => router.reload({ only: ['procurements', 'importLogs', 'stats'] }), 1000);
        });
}

function pollImportStatus(logId) {
    const interval = setInterval(() => {
        router.reload({ 
            only: ['importLogs', 'procurements', 'stats'], 
            onSuccess: () => {
                const log = props.importLogs.find(l => l.id === logId);
                if (log && log.status !== 'processing') {
                    clearInterval(interval); 
                    isImporting.value = false; 
                    progressPct.value = 100;
                    importResult.value = { 
                        success_count: log.success_count, 
                        failure_count: log.failure_count, 
                        error_file_url: log.has_error_log ? `/report/error-log/${logId}` : null 
                    };
                }
            }
        });
    }, 3000);
}

function stopListening() { 
    if (echoChannel && importLogId.value) { 
        echoInstance?.leave(`import.${importLogId.value}`); 
        echoChannel = null; 
    } 
}

onUnmounted(() => stopListening());

// Helper for status badge styling inside procurement table
const statusConfig = {
    'RP':    { cls: 'bg-[#FACC15] text-black border-2 border-black px-2 py-0.5 shadow-[2px_2px_0px_0px_#000]', label: 'Request Purchasing' },
    'TE':    { cls: 'bg-[#22D3EE] text-black border-2 border-black px-2 py-0.5 shadow-[2px_2px_0px_0px_#000]', label: 'Technical Evaluation' },
    'RE-TE': { cls: 'bg-[#FF80FF] text-black border-2 border-black px-2 py-0.5 shadow-[2px_2px_0px_0px_#000]', label: 'Re-Technical Evaluation' },
    'PO':    { cls: 'bg-[#4ADE80] text-black border-2 border-black px-2 py-0.5 shadow-[2px_2px_0px_0px_#000]', label: 'Purchase Order' },
};
function getStatus(status) { 
    return statusConfig[status] || { cls: 'bg-gray-500 text-white border-2 border-black', label: status }; 
}

// Calculate progress percentage for import logs
function getLogProgress(log) {
    if (!log.total_rows) return 0;
    return Math.round((log.processed_rows / log.total_rows) * 100);
}
</script>

<template>
    <AuthenticatedLayout title="Control Hub Pelaporan">
        <div class="space-y-8 mb-12">
            
            <!-- ═══════════════════════════════════════════ -->
            <!-- 3-COLUMN GRID LAYOUT FOR ACTIONS & UPLOAD    -->
            <!-- ═══════════════════════════════════════════ -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- COLUMN 1: Export Buttons & Template -->
                <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_#000] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 border-b-2 border-black pb-3 mb-4">
                            <div class="w-3 h-3 bg-black"></div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-black">Ekspor Laporan</h3>
                        </div>
                        <p class="text-xs font-mono font-bold text-gray-600 mb-6 leading-relaxed">
                            Unduh rangkuman data monitoring dalam format Excel atau PDF Folio/F4 Landscape.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <a 
                            href="/report/export/excel" 
                            class="w-full h-11 bg-[#4ADE80] text-black font-black border-4 border-black uppercase tracking-wider text-xs shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all decoration-none flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                        <a 
                            href="/report/export/pdf" 
                            class="w-full h-11 bg-[#FF80FF] text-black font-black border-4 border-black uppercase tracking-wider text-xs shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all decoration-none flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </a>
                        <a 
                            href="/report/template" 
                            class="w-full h-11 bg-white text-black font-bold border-2 border-black uppercase tracking-wider text-xs hover:bg-gray-100 transition-all decoration-none flex items-center justify-center gap-2"
                        >
                            <i class="bi bi-download"></i> Template Excel
                        </a>
                    </div>
                </div>

                <!-- COLUMNS 2 & 3: Upload Dropzone & Submitter -->
                <div class="md:col-span-2 bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_#000] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 border-b-2 border-black pb-3 mb-4">
                            <div class="w-3 h-3 bg-black"></div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-black">Unggah & Import Data</h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-stretch">
                        <!-- Dropzone -->
                        <div 
                            @dragover.prevent="dragOver = true" 
                            @dragleave.prevent="dragOver = false" 
                            @drop.prevent="onDrop" 
                            @click="fileInputRef?.click()"
                            :class="[
                                'border-4 border-dashed p-6 text-center cursor-pointer transition duration-150 flex flex-col items-center justify-center', 
                                dragOver ? 'border-[#FACC15] bg-[#FACC15]/10' : 'border-black hover:bg-gray-50'
                            ]"
                        >
                            <input ref="fileInputRef" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFileSelect">
                            <i class="bi bi-cloud-arrow-up text-3xl text-black mb-2"></i>
                            
                            <p v-if="!selectedFileName" class="text-xs font-black uppercase tracking-wider text-black">
                                Drag & drop atau klik
                            </p>
                            <p v-if="!selectedFileName" class="text-[10px] font-mono text-gray-500 mt-1">
                                Format: .xlsx, .xls, .csv (Maks 50MB)
                            </p>

                            <div v-if="selectedFileName" class="w-full">
                                <p class="text-xs font-mono font-black text-black truncate max-w-[200px] mx-auto">
                                    {{ selectedFileName }}
                                </p>
                                <button @click.stop="clearFile" class="text-[10px] text-red-600 font-bold uppercase hover:underline mt-1">
                                    Hapus file
                                </button>
                            </div>
                        </div>

                        <!-- Import Action Button -->
                        <div class="flex flex-col justify-center gap-4">
                            <button 
                                @click="submitImport" 
                                :disabled="!importForm.file || importForm.processing"
                                class="w-full h-12 text-black font-black border-4 border-black uppercase tracking-wider text-xs flex items-center justify-center gap-2 transition-all"
                                :class="[
                                    importForm.file && !importForm.processing 
                                        ? 'bg-[#22D3EE] shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] cursor-pointer' 
                                        : 'bg-gray-200 text-gray-400 border-gray-300 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="importForm.processing">Memproses...</span>
                                <span v-else><i class="bi bi-upload"></i> Proses Import</span>
                            </button>

                            <div v-if="importForm.errors.file" class="bg-[#FF80FF]/25 border-2 border-black p-3 text-xs font-bold text-black font-mono">
                                {{ importForm.errors.file }}
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar during upload / import -->
                    <div v-if="isImporting" class="mt-6 bg-[#F4F4F0] border-2 border-black p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-black uppercase text-black">Mengimport Data...</span>
                            <span class="text-xs font-mono font-black text-black">{{ progressPct }}%</span>
                        </div>
                        <div class="w-full h-4 bg-white border-2 border-black overflow-hidden relative">
                            <div 
                                class="h-full bg-[#4ADE80] transition-all duration-300 ease-out border-r border-black" 
                                :style="{ width: progressPct + '%' }"
                            ></div>
                        </div>
                        <p class="text-[10px] font-mono text-gray-500 mt-2">
                            Proses sedang berjalan di latar belakang. Anda dapat melihat status antrean di bawah.
                        </p>
                    </div>

                    <!-- Import Result Notification -->
                    <div 
                        v-if="importResult" 
                        class="mt-6 border-2 border-black p-4 flex flex-wrap items-center justify-between gap-4"
                        :class="importResult.failure_count > 0 ? 'bg-[#FF80FF]/20' : 'bg-[#4ADE80]/20'"
                    >
                        <div class="flex-1">
                            <h4 class="text-xs font-black uppercase tracking-wider text-black">Hasil Import Selesai</h4>
                            <p class="text-xs font-mono font-bold text-gray-700 mt-1">
                                {{ importResult.success_count }} baris berhasil import.
                                <span v-if="importResult.failure_count > 0" class="text-red-600 ml-1 font-black">
                                    ({{ importResult.failure_count }} baris gagal)
                                </span>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a 
                                v-if="importResult.error_file_url" 
                                :href="'/report/error-log/' + importLogId"
                                class="h-8 px-3 border-2 border-black bg-red-500 text-white text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] transition-all flex items-center"
                            >
                                Error Log
                            </a>
                            <button 
                                @click="importResult = null"
                                class="h-8 px-3 border-2 border-black bg-white text-black text-[10px] font-bold uppercase hover:bg-gray-100"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════ -->
            <!-- QUEUE PROGRESS LOGGING TABLE (Riwayat)     -->
            <!-- ═══════════════════════════════════════════ -->
            <section class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_#000] overflow-hidden">
                <div class="p-6 border-b-4 border-black">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-black"></div>
                        <h3 class="text-lg font-black uppercase tracking-wider text-black">Log Progress Antrean</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-black text-white font-black border-b-2 border-black">
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-16">No</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Nama Berkas</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Tanggal</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-32">Sukses</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-32">Gagal</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-48">Progress</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-32">Status</th>
                                <th class="p-4 text-center text-xs font-black uppercase w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold text-sm">
                            <tr v-for="(log, idx) in importLogs" :key="log.id" class="hover:bg-[#F4F4F0] transition duration-100">
                                <td class="p-4 border-r-2 border-black text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-4 border-r-2 border-black font-mono text-xs">{{ log.file_name }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-xs">{{ log.created_at }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-green-600 font-black">+{{ log.success_count }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-red-600 font-black">-{{ log.failure_count }}</td>
                                <td class="p-4 border-r-2 border-black text-center">
                                    <div class="flex items-center gap-2">
                                        <!-- Inline geometric progress bar block -->
                                        <div class="flex-grow h-4 bg-white border-2 border-black overflow-hidden relative min-w-[80px]">
                                            <div 
                                                class="h-full bg-[#4ADE80] border-r-2 border-black transition-all duration-300"
                                                :style="{ width: getLogProgress(log) + '%' }"
                                            ></div>
                                        </div>
                                        <span class="text-xs font-mono font-black text-black">{{ getLogProgress(log) }}%</span>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-black text-center">
                                    <span 
                                        v-if="log.status === 'completed'" 
                                        class="inline-block border-2 border-black bg-[#4ADE80] text-black px-2 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]"
                                    >
                                        SELESAI
                                    </span>
                                    <span 
                                        v-else-if="log.status === 'processing'" 
                                        class="inline-block border-2 border-black bg-[#22D3EE] text-black px-2 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000] animate-pulse"
                                    >
                                        PROSES
                                    </span>
                                    <span 
                                        v-else 
                                        class="inline-block border-2 border-black bg-[#FF80FF] text-black px-2 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-[2px_2px_0px_0px_#000]"
                                    >
                                        GAGAL
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a 
                                        v-if="log.has_error_log" 
                                        :href="'/report/error-log/' + log.id"
                                        class="inline-block h-8 px-3 border-2 border-black bg-[#FF80FF] text-black font-black text-[10px] uppercase shadow-[2px_2px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_#000] transition-all decoration-none flex items-center justify-center"
                                    >
                                        Error Log
                                    </a>
                                    <span v-else class="text-xs font-mono font-bold text-gray-400">—</span>
                                </td>
                            </tr>
                            <tr v-if="importLogs.length === 0">
                                <td colspan="8" class="p-8 text-center text-gray-500 font-mono">
                                    Belum ada riwayat proses import berkas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- DATA TABLE (Procurement View) -->
            <section class="bg-white border-4 border-black shadow-[6px_6px_0px_0px_#000] overflow-hidden">
                <div class="p-6 border-b-4 border-black flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-black"></div>
                        <h3 class="text-lg font-black uppercase tracking-wider text-black">
                            Daftar Procurement 
                            <span class="text-xs font-mono font-normal text-gray-500 ml-1">({{ filteredProcurements.length }} records)</span>
                        </h3>
                    </div>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Cari kode / barang / status..."
                        class="h-10 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-xs w-full sm:w-64" 
                    />
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-black text-white font-black border-b-2 border-black">
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase w-16">No</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Kode</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Nama Barang</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Vendor</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Tgl TE</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Tgl RE-TE</th>
                                <th class="p-4 border-r-2 border-black text-center text-xs font-black uppercase">Tgl PO</th>
                                <th class="p-4 text-center text-xs font-black uppercase w-48">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold text-sm">
                            <tr v-for="(item, idx) in filteredProcurements" :key="item.id" class="hover:bg-[#F4F4F0] transition duration-100">
                                <td class="p-4 border-r-2 border-black text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono font-black text-black select-all">{{ item.kode_pengadaan }}</td>
                                <td class="p-4 border-r-2 border-black">{{ item.nama_barang }}</td>
                                <td class="p-4 border-r-2 border-black">{{ item.vendor || '—' }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-xs">{{ item.tanggal_te || '—' }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-xs">{{ item.tanggal_rete || '—' }}</td>
                                <td class="p-4 border-r-2 border-black text-center font-mono text-xs">{{ item.tanggal_po || '—' }}</td>
                                <td class="p-4 text-center">
                                    <span :class="getStatus(item.status).cls" class="inline-block text-[11px] font-black uppercase tracking-wider py-1 px-3">
                                        {{ getStatus(item.status).label }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="filteredProcurements.length === 0">
                                <td colspan="8" class="p-8 text-center text-gray-500 font-mono">
                                    Belum ada data procurement.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </AuthenticatedLayout>
</template>
