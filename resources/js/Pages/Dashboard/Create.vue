<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    status: 'RP',
    kode_pengadaan: '',
    nama_barang: '',
    vendor: '',
    quantity: '',
    departemen: '',
    keterangan: '',
    hasil_evaluasi: '',
    catatan: '',
    tanggal: new Date().toISOString().split('T')[0],
});

// Dinamic labels for Kode Pengadaan based on selected phase
const labelKode = computed(() => {
    if (form.status === 'RP') return 'Kode RP';
    if (form.status === 'TE') return 'Kode TE';
    if (form.status === 'RE-TE') return 'Kode Re-TE';
    return 'Kode PO';
});

function submit() {
    form.post('/procurement/store');
}
</script>

<template>
    <AuthenticatedLayout title="Tambah Data Procurement">
        <div class="max-w-[580px] mx-auto bg-white border-4 border-black p-6 md:p-8 shadow-[8px_8px_0px_0px_#000] mt-6 mb-12">
            <!-- stark section indicator -->
            <div class="flex items-center gap-3 border-b-4 border-black pb-4 mb-6">
                <div class="w-5 h-5 bg-black"></div>
                <h3 class="text-xl font-black uppercase tracking-wider text-black">Formulir Pengadaan</h3>
            </div>

            <!-- Global Validation Notice -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-[#FF80FF]/20 border-2 border-black p-4 text-xs font-bold space-y-1">
                <div class="font-black uppercase tracking-wider text-black mb-1">Periksa Input Berikut:</div>
                <div v-for="(err, key) in form.errors" :key="key" class="font-mono text-black">
                    · {{ err }}
                </div>
            </div>
            
            <form @submit.prevent="submit" class="space-y-5 text-left">
                <!-- Dropdown Status Fase -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Status Fase</label>
                    <select v-model="form.status" class="w-full h-11 px-3 border-2 border-black font-black focus:ring-0 focus:outline-none bg-white text-sm">
                        <option value="RP">Request Purchasing (RP)</option>
                        <option value="TE">Technical Evaluation (TE)</option>
                        <option value="RE-TE">Re-Technical Evaluation (RE-TE)</option>
                        <option value="PO">Purchase Order (PO)</option>
                    </select>
                </div>

                <!-- Kode Pengadaan -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">{{ labelKode }}</label>
                    <input type="text" v-model="form.kode_pengadaan" placeholder="Contoh: PRQ-001" class="w-full h-11 px-3 border-2 border-black font-mono focus:ring-0 focus:outline-none bg-white text-sm" :class="{ 'border-red-500 bg-red-50': form.errors.kode_pengadaan }" />
                </div>

                <!-- Nama Barang (Tampil pada RP, RE-TE, PO) -->
                <div v-if="['RP', 'RE-TE', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Nama Barang</label>
                    <input type="text" v-model="form.nama_barang" placeholder="Nama barang" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" :class="{ 'border-red-500 bg-red-50': form.errors.nama_barang }" />
                </div>

                <!-- Vendor (Tampil pada TE, RE-TE, PO) -->
                <div v-if="['TE', 'RE-TE', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Vendor</label>
                    <input type="text" v-model="form.vendor" placeholder="Nama vendor" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" :class="{ 'border-red-500 bg-red-50': form.errors.vendor }" />
                </div>

                <!-- Quantity (Tampil pada RP, PO) -->
                <div v-if="['RP', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Quantity</label>
                    <input type="text" v-model="form.quantity" placeholder="Contoh: 10 Unit" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" :class="{ 'border-red-500 bg-red-50': form.errors.quantity }" />
                </div>

                <!-- Departemen (Tampil pada RP, PO) -->
                <div v-if="['RP', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Departemen</label>
                    <input type="text" v-model="form.departemen" placeholder="Departemen peminta" class="w-full h-11 px-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm" :class="{ 'border-red-500 bg-red-50': form.errors.departemen }" />
                </div>

                <!-- Keterangan (Tampil pada RP, PO) -->
                <div v-if="['RP', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Keterangan</label>
                    <textarea v-model="form.keterangan" placeholder="Keterangan pengadaan" rows="3" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y" :class="{ 'border-red-500 bg-red-50': form.errors.keterangan }"></textarea>
                </div>

                <!-- Hasil Evaluasi (Tampil pada TE, PO) -->
                <div v-if="['TE', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Hasil Evaluasi</label>
                    <textarea v-model="form.hasil_evaluasi" placeholder="Hasil evaluasi teknis" rows="3" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y" :class="{ 'border-red-500 bg-red-50': form.errors.hasil_evaluasi }"></textarea>
                </div>

                <!-- Catatan (Tampil pada RE-TE, PO) -->
                <div v-if="['RE-TE', 'PO'].includes(form.status)">
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Catatan</label>
                    <textarea v-model="form.catatan" placeholder="Catatan evaluasi ulang" rows="3" class="w-full p-3 border-2 border-black font-bold focus:ring-0 focus:outline-none bg-white text-sm resize-y" :class="{ 'border-red-500 bg-red-50': form.errors.catatan }"></textarea>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1">Tanggal</label>
                    <input type="date" v-model="form.tanggal" class="w-full h-11 px-3 border-2 border-black font-mono focus:ring-0 focus:outline-none bg-white text-sm" />
                </div>

                <!-- Aksi -->
                <div class="pt-4 flex flex-col gap-3">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="w-full h-12 bg-[#4ADE80] text-black font-black border-4 border-black uppercase tracking-wider text-xs shadow-[4px_4px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:shadow-none transition-all cursor-pointer flex items-center justify-center gap-2"
                    >
                        <i class="bi bi-save"></i>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                    </button>

                    <Link 
                        href="/dashboard" 
                        class="w-full h-12 bg-[#FF80FF] text-black font-black border-4 border-black uppercase tracking-wider text-xs shadow-[4px_4px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:shadow-none transition-all decoration-none flex items-center justify-center gap-2"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
