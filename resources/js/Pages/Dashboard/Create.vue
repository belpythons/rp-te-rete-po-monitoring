<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    status: 'RP',
    no: '',
    rp_number: '',
    description: '',
    date_created: new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }),
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
});

function submit() {
    form.post('/procurement/store');
}
</script>

<template>
    <AuthenticatedLayout title="Tambah Data Procurement">
        <div class="max-w-[800px] mx-auto bg-white rounded-2xl border border-slate-100 p-6 md:p-8 shadow-lg mt-6 mb-12">
            <!-- Header Indicator -->
            <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-6">
                <div class="w-3 h-6 bg-blue-600 rounded-full"></div>
                <h3 class="text-base font-bold text-slate-800">Formulir Pengadaan</h3>
            </div>

            <!-- Global Validation Errors -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-xs space-y-1 font-semibold">
                <div class="font-bold uppercase tracking-wider mb-1">Periksa Input Berikut:</div>
                <div v-for="(err, key) in form.errors" :key="key">
                    • {{ err }}
                </div>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6 text-left">
                <!-- 2 Column Grid for Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Status Dropdown -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Fase</label>
                        <select v-model="form.status" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white">
                            <option value="RP">Request Purchasing (RP)</option>
                            <option value="TE">Technical Evaluation (TE)</option>
                            <option value="RE-TE">Re-Technical Evaluation (RE-TE)</option>
                            <option value="PO">Purchase Order (PO)</option>
                        </select>
                    </div>

                    <!-- No -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No (Serial)</label>
                        <input type="text" v-model="form.no" required placeholder="Contoh: 49" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- RP Number -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Pengadaan (RP Number)</label>
                        <input type="text" v-model="form.rp_number" required placeholder="Contoh: 1000004316" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm font-mono" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description (Deskripsi Barang)</label>
                        <input type="text" v-model="form.description" required placeholder="Deskripsi barang" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- Date Created -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date Created</label>
                        <input type="text" v-model="form.date_created" required class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- Send for Approval General Director -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Send Gen Dir Approval</label>
                        <input type="text" v-model="form.send_for_approval_general_director" placeholder="Tanggal send approval" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- Buyer -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Buyer (Inisial)</label>
                        <input type="text" v-model="form.buyer" placeholder="Contoh: SU" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- TE In -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">TE In Date</label>
                        <input type="text" v-model="form.te_in" placeholder="Tanggal TE In" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- TE Out -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">TE Out Date</label>
                        <input type="text" v-model="form.te_out" placeholder="Tanggal TE Out" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- RE-TE -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RE-TE Date</label>
                        <input type="text" v-model="form.re_te" placeholder="Tanggal RE-TE" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- PO -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">PO Date/Number</label>
                        <input type="text" v-model="form.po" placeholder="Tanggal PO / Nomor PO" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- SO -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SO Date/Number</label>
                        <input type="text" v-model="form.so" placeholder="Tanggal SO / Nomor SO" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- QC -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">QC Date/Result</label>
                        <input type="text" v-model="form.qc" placeholder="Tanggal QC" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- Delivery -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Delivery Date</label>
                        <input type="text" v-model="form.delivery" placeholder="Tanggal Delivery" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- RR -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">RR Date</label>
                        <input type="text" v-model="form.rr" placeholder="Tanggal RR" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>

                    <!-- Vendor -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Vendor Name</label>
                        <input type="text" v-model="form.vendor" placeholder="Nama vendor" class="w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="w-full sm:flex-grow h-11 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition flex items-center justify-center gap-2 cursor-pointer shadow-md"
                    >
                        <i class="bi bi-save"></i>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                    </button>

                    <a 
                        href="/dashboard" 
                        class="w-full sm:w-32 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg flex items-center justify-center gap-2 transition cursor-pointer decoration-none text-sm"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
