<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

defineProps({
    title: {
        type: String,
        default: 'LAPORAN (REPORT)'
    }
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const navItems = computed(() => [
    { href: '/dashboard', label: 'Dashboard', icon: 'bi bi-speedometer2' },
    { href: '/dashboard?status=RP', label: 'Request Purchasing', icon: 'bi bi-file-earmark-text' },
    { href: '/dashboard?status=TE', label: 'Technical Evaluation', icon: 'bi bi-clipboard-check' },
    { href: '/dashboard?status=RE-TE', label: 'Re-Technical Evaluation', icon: 'bi bi-arrow-repeat' },
    { href: '/dashboard?status=PO', label: 'Purchase Order', icon: 'bi bi-bag-check' },
    { href: '/report', label: 'Laporan', icon: 'bi bi-bar-chart-fill' },
]);

const isActive = (item) => {
    if (item.href === '/dashboard') {
        return page.component === 'Dashboard/Index' && !page.url.includes('status=');
    }
    if (item.href.includes('status=')) {
        const status = item.href.split('status=')[1];
        return page.component === 'Dashboard/Index' && page.url.includes(`status=${status}`);
    }
    if (item.href.startsWith('/report')) {
        return page.component === 'Report/Index';
    }
    return false;
};

const csrfToken = computed(() => {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
});

// Toast / Flash Logic (Neo-Brutalist Style)
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success'); // 'success' or 'error'

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toastMessage.value = flash.success;
            toastType.value = 'success';
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 4000);
        } else if (flash?.error) {
            toastMessage.value = flash.error;
            toastType.value = 'error';
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 4000);
        }
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <div class="min-h-screen bg-[#F4F4F0] text-black font-sans antialiased flex flex-col md:flex-row">
        <!-- ═══════════════════════════════════════════ -->
        <!-- SIDEBAR - Yellow background, black border  -->
        <!-- ═══════════════════════════════════════════ -->
        <aside class="w-full md:w-72 md:fixed md:top-0 md:left-0 md:h-screen bg-[#FACC15] border-b-4 md:border-b-0 md:border-r-4 border-black p-6 flex flex-col justify-between z-[100] overflow-y-auto">
            <div>
                <!-- Stark White Branded Logo Box -->
                <div class="bg-white border-4 border-black p-4 font-black shadow-[4px_4px_0px_0px_#000] text-center mb-8 uppercase tracking-widest text-lg md:text-xl">
                    <div class="text-xs font-mono text-gray-500 tracking-normal mb-1">PT. KMI</div>
                    P-MONITORING
                </div>

                <!-- Navigation Links as raised Neo-brutalist buttons -->
                <nav class="space-y-4">
                    <a
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="block border-2 border-black p-3 font-bold shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all duration-150 text-sm flex items-center gap-3 decoration-none text-black"
                        :class="isActive(item) ? 'bg-white' : 'bg-[#22D3EE]'"
                    >
                        <i :class="[item.icon, 'text-base']"></i>
                        <span>{{ item.label }}</span>
                    </a>
                </nav>
            </div>

            <!-- Logout Section -->
            <div class="mt-8 border-t-2 border-black pt-6 flex flex-col gap-4">
                <form method="POST" action="/logout" class="w-full">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <button
                        type="submit"
                        class="w-full border-4 border-black bg-[#FF80FF] text-black font-black p-3 shadow-[4px_4px_0px_0px_#000] hover:-translate-x-1 hover:-translate-y-1 hover:shadow-none transition-all duration-150 cursor-pointer text-sm tracking-wider uppercase"
                    >
                        <i class="bi bi-box-arrow-right mr-1.5 font-bold"></i>
                        Logout
                    </button>
                </form>
                <div class="bg-white border-2 border-black py-2 px-3 text-center font-black text-xs shadow-[2px_2px_0px_0px_#000] tracking-wide uppercase">
                    User: {{ user?.name || 'Admin' }}
                </div>
            </div>
        </aside>

        <!-- ═══════════════════════════════════════════ -->
        <!-- MAIN CONTENT AREA                          -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="flex-grow md:ml-72 p-6 md:p-8 flex flex-col min-h-screen">
            <!-- Topbar (Stark Header) -->
            <header class="bg-[#22D3EE] border-4 border-black p-4 md:p-5 shadow-[6px_6px_0px_0px_#000] mb-8 flex justify-between items-center">
                <h1 class="text-sm md:text-base font-black uppercase tracking-wider">
                    PROCUREMENT SYSTEM - PT.KMI
                </h1>
                <span class="hidden md:inline bg-black text-[#FACC15] text-[10px] font-mono px-2 py-0.5 border border-black font-bold uppercase">
                    SYS.V1.2
                </span>
            </header>

            <!-- Page Title -->
            <div class="mb-6">
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    {{ title }}
                </h2>
            </div>

            <!-- Main view slot -->
            <main class="flex-grow">
                <slot />
            </main>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- GLOBAL NEO-BRUTALIST TOAST NOTIFICATION    -->
        <!-- ═══════════════════════════════════════════ -->
        <Transition name="toast-slide">
            <div
                v-if="showToast"
                class="fixed bottom-6 right-6 z-[9999] w-[340px] bg-white border-4 border-black p-4 shadow-[6px_6px_0px_0px_#000] flex items-start gap-3"
            >
                <!-- Status color indicator -->
                <div
                    class="w-6 h-6 rounded-none flex items-center justify-center border-2 border-black flex-shrink-0"
                    :class="toastType === 'success' ? 'bg-[#4ADE80]' : 'bg-[#FF80FF]'"
                >
                    <i v-if="toastType === 'success'" class="bi bi-check-lg text-sm font-black"></i>
                    <i v-else class="bi bi-x-lg text-xs font-black"></i>
                </div>

                <!-- Text -->
                <div class="flex-grow">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black">
                        {{ toastType === 'success' ? 'BERHASIL' : 'GAGAL' }}
                    </h4>
                    <p class="text-xs font-mono font-bold text-gray-700 mt-1 leading-relaxed">
                        {{ toastMessage }}
                    </p>
                </div>

                <!-- Close -->
                <button
                    @click="showToast = false"
                    class="text-black font-black hover:text-red-600 transition duration-150 flex-shrink-0"
                >
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* TOAST TRANSITION */
.toast-slide-enter-active,
.toast-slide-leave-active {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-slide-enter-from {
    transform: translateY(15px);
    opacity: 0;
}
.toast-slide-leave-to {
    opacity: 0;
}
</style>
