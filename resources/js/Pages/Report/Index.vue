<script setup>
import { ref, computed, onUnmounted, getCurrentInstance } from 'vue';
import { useForm, usePage, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    procurements:    { type: Object, default: () => ({}) },
    importLogs:      { type: Array, default: () => [] },
    stats:           { type: Object, default: () => ({}) },
    availableMonths: { type: Array, default: () => [] },
    filters:         { type: Object, default: () => ({}) },
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
const activeMonth = ref(props.filters.month_year || '');

// Backend filters month_year change handler
function handleMonthChange() {
    router.get('/report', {
        month_year: activeMonth.value || undefined,
    }, {
        preserveState: true,
        replace: true
    });
}

// Client-side quick filter on active paginated records
const searchQuery = ref('');
const filteredProcurements = computed(() => {
    let data = [];
    if (Array.isArray(props.procurements)) {
        data = props.procurements;
    } else {
        data = props.procurements.data || [];
    }
    
    if (!searchQuery.value) return data;
    const q = searchQuery.value.toLowerCase();
    return data.filter(p =>
        (p.rp_number && p.rp_number.toLowerCase().includes(q)) ||
        (p.description && p.description.toLowerCase().includes(q)) ||
        (p.vendor && p.vendor.toLowerCase().includes(q)) ||
        (p.status && p.status.toLowerCase().includes(q)) ||
        (p.phase && p.phase.toLowerCase().includes(q))
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
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                            <div class="w-3 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-base font-bold text-slate-800">Ekspor Laporan</h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                            Unduh rangkuman data monitoring dalam format Excel atau PDF Folio/F4 Landscape.
                        </p>

                        <!-- Bulan & Tahun Filter Dropdown (Above PDF/Excel buttons) -->
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bulan & Tahun Laporan:</label>
                            <select 
                                v-model="activeMonth" 
                                @change="handleMonthChange" 
                                class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white"
                            >
                                <option value="">Semua Bulan</option>
                                <option v-for="m in availableMonths" :key="m.value" :value="m.value">
                                    {{ m.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a 
                            :href="activeMonth ? `/report/export/excel?month_year=${activeMonth}` : '/report/export/excel'" 
                            class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-all decoration-none flex items-center justify-center gap-2 cursor-pointer shadow-sm text-sm font-sans"
                        >
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                        <a 
                            :href="activeMonth ? `/report/export/pdf?month_year=${activeMonth}` : '/report/export/pdf'" 
                            class="w-full h-11 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all decoration-none flex items-center justify-center gap-2 cursor-pointer shadow-sm text-sm font-sans"
                        >
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </a>
                        <a 
                            href="/report/template" 
                            class="w-full h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition decoration-none flex items-center justify-center gap-2 text-sm font-sans"
                        >
                            <i class="bi bi-download"></i> Template Excel
                        </a>
                    </div>
                </div>

                <!-- COLUMNS 2 & 3: Upload Dropzone & Submitter -->
                <div class="md:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                            <div class="w-3 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-base font-bold text-slate-800">Unggah & Import Data</h3>
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
                                'border-2 border-dashed p-6 text-center cursor-pointer transition duration-150 flex flex-col items-center justify-center rounded-xl', 
                                dragOver ? 'border-blue-500 bg-blue-50/50' : 'border-slate-300 hover:bg-slate-50/50'
                            ]"
                        >
                            <input ref="fileInputRef" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFileSelect">
                            <i class="bi bi-cloud-arrow-up text-3xl text-slate-500 mb-2"></i>
                            
                            <p v-if="!selectedFileName" class="text-xs font-bold text-slate-700 uppercase">
                                Drag & drop atau klik
                            </p>
                            <p v-if="!selectedFileName" class="text-[10px] text-slate-400 mt-1">
                                Format: .xlsx, .xls, .csv (Maks 50MB)
                            </p>

                            <div v-if="selectedFileName" class="w-full">
                                <p class="text-xs font-mono font-bold text-slate-700 truncate max-w-[200px] mx-auto">
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
                                class="w-full h-12 text-white font-bold rounded-lg flex items-center justify-center gap-2 transition-all text-sm shadow-sm"
                                :class="[
                                    importForm.file && !importForm.processing 
                                        ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' 
                                        : 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="importForm.processing">Memproses...</span>
                                <span v-else><i class="bi bi-upload"></i> Proses Import</span>
                            </button>

                            <div v-if="importForm.errors.file" class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-xs font-semibold">
                                {{ importForm.errors.file }}
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar during upload / import -->
                    <div v-if="isImporting" class="mt-6 bg-slate-50 border border-slate-100 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-700">Mengimport Data...</span>
                            <span class="text-xs font-mono font-bold text-slate-700">{{ progressPct }}%</span>
                        </div>
                        <div class="w-full h-3 bg-white border border-slate-200 rounded-full overflow-hidden relative">
                            <div 
                                class="h-full bg-emerald-500 transition-all duration-300 ease-out" 
                                :style="{ width: progressPct + '%' }"
                            ></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2">
                            Proses sedang berjalan di latar belakang. Anda dapat melihat status antrean di bawah.
                        </p>
                    </div>

                    <!-- Import Result Notification -->
                    <div 
                        v-if="importResult" 
                        class="mt-6 border p-4 rounded-xl flex flex-wrap items-center justify-between gap-4"
                        :class="importResult.failure_count > 0 ? 'bg-red-50 border-red-100 text-red-800' : 'bg-emerald-50 border-emerald-100 text-emerald-800'"
                    >
                        <div class="flex-1">
                            <h4 class="text-xs font-bold uppercase tracking-wider">Hasil Import Selesai</h4>
                            <p class="text-xs font-mono font-semibold mt-1">
                                {{ importResult.success_count }} baris berhasil import.
                                <span v-if="importResult.failure_count > 0" class="text-red-600 ml-1 font-bold">
                                    ({{ importResult.failure_count }} baris gagal)
                                </span>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a 
                                v-if="importResult.error_file_url" 
                                :href="'/report/error-log/' + importLogId"
                                class="h-8 px-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold uppercase rounded-lg shadow-sm flex items-center justify-center decoration-none"
                            >
                                Error Log
                            </a>
                            <button 
                                @click="importResult = null"
                                class="h-8 px-3 bg-white text-slate-700 border border-slate-200 text-[10px] font-bold uppercase rounded-lg hover:bg-slate-50"
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
            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-base font-bold text-slate-800">Log Progress Antrean</h3>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[1200px]">
                        <thead>
                            <tr class="bg-slate-800 text-white border-b border-slate-700">
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-16">No</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider" style="min-width: 250px">Nama Berkas</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-32">Sukses</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-32">Gagal</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-48">Progress</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-32">Status</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                            <tr v-for="(log, idx) in importLogs" :key="log.id" class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 text-center font-mono text-xs">{{ idx + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-xs whitespace-normal text-left" :title="log.file_name">{{ log.file_name }}</td>
                                <td class="py-3 px-4 text-center font-mono text-xs text-slate-500">{{ log.created_at }}</td>
                                <td class="py-3 px-4 text-center font-mono text-emerald-600 font-bold">+{{ log.success_count }}</td>
                                <td class="py-3 px-4 text-center font-mono text-red-500 font-bold">-{{ log.failure_count }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-grow h-3 bg-slate-100 rounded-full overflow-hidden relative min-w-[80px]">
                                            <div 
                                                class="h-full bg-emerald-500 transition-all duration-300"
                                                :style="{ width: getLogProgress(log) + '%' }"
                                            ></div>
                                        </div>
                                        <span class="text-xs font-mono font-bold text-slate-700">{{ getLogProgress(log) }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span 
                                        v-if="log.status === 'completed'" 
                                        class="inline-block bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                    >
                                        SELESAI
                                    </span>
                                    <span 
                                        v-else-if="log.status === 'processing'" 
                                        class="inline-block bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider animate-pulse"
                                    >
                                        PROSES
                                    </span>
                                    <span 
                                        v-else 
                                        class="inline-block bg-red-50 text-red-700 border border-red-200 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                    >
                                        GAGAL
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a 
                                        v-if="log.has_error_log" 
                                        :href="'/report/error-log/' + log.id"
                                        class="inline-block h-8 px-3 bg-red-600 hover:bg-red-700 text-white font-semibold text-[10px] uppercase rounded-lg shadow-sm flex items-center justify-center decoration-none"
                                    >
                                        Error Log
                                    </a>
                                    <span v-else class="text-xs font-mono font-semibold text-slate-400">—</span>
                                </td>
                            </tr>
                            <tr v-if="importLogs.length === 0">
                                <td colspan="8" class="p-8 text-center text-slate-400 font-mono text-xs">
                                    Belum ada riwayat proses import berkas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- DATA TABLE (Procurement View - Solid black border, white bg, shadow-md, text-base font) -->
            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-6 bg-slate-800 rounded-full"></div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Daftar Laporan Procurement 
                            <span class="text-xs font-mono font-normal text-slate-500 ml-1">({{ filteredProcurements.length }} records)</span>
                        </h3>
                    </div>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Cari kode / deskripsi / status..."
                        class="h-10 px-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-xs w-full sm:w-64 bg-white" 
                    />
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[1200px]">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider font-sans">No</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider font-sans">Kode (RP)</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider font-sans" style="min-width: 320px">Deskripsi Barang</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider font-sans">Tanggal Created</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider font-sans" style="min-width: 200px">Vendor</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider font-sans">Fase</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider font-sans">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-slate-200 text-slate-900 text-xs">
                            <tr v-for="item in filteredProcurements" :key="item.id" class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 text-center font-mono text-xs">{{ item.no }}</td>
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-900 select-all text-xs">{{ item.rp_number }}</td>
                                <td class="py-3 px-4 text-left font-medium text-xs whitespace-normal" :title="item.description">{{ item.description }}</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-600 text-xs">{{ item.date_created || '—' }}</td>
                                <td class="py-3 px-4 text-left text-slate-600 text-xs whitespace-normal" :title="item.vendor">{{ item.vendor || '—' }}</td>
                                <td class="py-3 px-4 text-center text-xs">
                                    <span class="inline-block bg-slate-100 text-slate-800 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">{{ item.phase }}</span>
                                </td>
                                <td class="py-3 px-4 text-center text-xs">
                                    <span v-if="item.status === 'Disetujui'" class="inline-block bg-green-50 text-green-700 border border-green-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Disetujui</span>
                                    <span v-else-if="item.status === 'Tidak Disetujui'" class="inline-block bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Tidak Disetujui</span>
                                    <span v-else class="inline-block bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">Pending</span>
                                </td>
                            </tr>
                            <tr v-if="filteredProcurements.length === 0">
                                <td colspan="7" class="p-8 text-center text-slate-400 font-mono text-xs">
                                    Belum ada data procurement.
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
                        <Link
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
                        ></Link>
                    </div>
                </div>
            </section>

        </div>
    </AuthenticatedLayout>
</template>
