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
    { href: '/request-purchasing', label: 'Request Purchasing', icon: 'bi bi-file-earmark-text' },
    { href: '/technical-evaluation', label: 'Technical Evaluation', icon: 'bi bi-clipboard-check' },
    { href: '/re-technical-evaluation', label: 'Re-Technical Evaluation', icon: 'bi bi-arrow-repeat' },
    { href: '/purchase-order', label: 'Purchase Order', icon: 'bi bi-bag-check' },
    { href: '/report', label: 'Laporan', icon: 'bi bi-bar-chart-fill' },
]);

const isActive = (item) => {
    if (item.href === '/dashboard') {
        return page.component === 'Dashboard/Index';
    }
    if (item.href === '/request-purchasing') {
        return page.component === 'RequestPurchasing/Index';
    }
    if (item.href === '/technical-evaluation') {
        return page.component === 'TechnicalEvaluation/Index';
    }
    if (item.href === '/re-technical-evaluation') {
        return page.component === 'ReTechnicalEvaluation/Index';
    }
    if (item.href === '/purchase-order') {
        return page.component === 'PurchaseOrder/Index';
    }
    if (item.href.startsWith('/report')) {
        return page.component === 'Report/Index';
    }
    return false;
};

const csrfToken = computed(() => {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
});

// Toast / Flash Logic
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

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
    <div class="min-h-screen text-slate-800 font-sans antialiased flex flex-col md:flex-row relative">
        <!-- Main Background Image -->
        <div 
            class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('/images/kmi1.jpg');"
        ></div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- SIDEBAR - Slate-800 background             -->
        <!-- ═══════════════════════════════════════════ -->
        <aside class="w-full md:w-72 md:fixed md:top-0 md:left-0 md:h-screen bg-slate-800 text-slate-100 p-6 flex flex-col justify-between z-30 shadow-xl overflow-y-auto">
            <div>
                <!-- KMI Logo & Title -->
                <div class="flex flex-col items-center mb-8 text-center border-b border-slate-700 pb-6">
                    <img 
                        src="/images/kmi-logo.png" 
                        alt="KMI Logo" 
                        class="h-16 w-auto mb-3 object-contain bg-white/10 p-1.5 rounded-lg"
                    />
                    <div class="text-xs font-mono text-slate-400 tracking-widest uppercase">PT. KMI</div>
                    <div class="text-sm font-semibold text-slate-200 tracking-wide mt-1">Kaltim Methanol Industri</div>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-2">
                    <a
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-150 decoration-none"
                        :class="isActive(item) 
                            ? 'bg-blue-600 text-white shadow-md' 
                            : 'text-slate-300 hover:bg-slate-700 hover:text-white'"
                    >
                        <i :class="[item.icon, 'text-base']"></i>
                        <span>{{ item.label }}</span>
                    </a>
                </nav>
            </div>

            <!-- Logout Section & User Profile -->
            <div class="mt-8 border-t border-slate-700 pt-6 flex flex-col gap-3">
                <div class="text-slate-400 text-xs font-mono text-center mb-1">
                    User: <span class="text-slate-200 font-semibold uppercase">{{ user?.name || 'Admin' }}</span>
                </div>
                <form method="POST" action="/logout" class="w-full">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <button
                        type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:bg-red-700 transition duration-150 cursor-pointer text-sm shadow-md flex items-center justify-center gap-2"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- ═══════════════════════════════════════════ -->
        <!-- MAIN CONTENT AREA                          -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="flex-grow md:ml-72 p-6 md:p-8 flex flex-col min-h-screen z-20 relative">
            <!-- Topbar Header -->
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 drop-shadow-sm">
                    Selamat Datang, Admin
                </h1>
            </header>

            <!-- Main view slot -->
            <main class="flex-grow flex flex-col">
                <slot />
            </main>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- GLOBAL TOAST NOTIFICATION                  -->
        <!-- ═══════════════════════════════════════════ -->
        <Transition name="toast-slide">
            <div
                v-if="showToast"
                class="fixed bottom-6 right-6 z-50 w-[340px] bg-white border border-slate-200 p-4 rounded-xl shadow-lg flex items-start gap-3"
            >
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                    :class="toastType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                >
                    <i v-if="toastType === 'success'" class="bi bi-check-lg text-lg font-bold"></i>
                    <i v-else class="bi bi-x-lg text-base font-bold"></i>
                </div>

                <!-- Text -->
                <div class="flex-grow">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                        {{ toastType === 'success' ? 'Berhasil' : 'Gagal' }}
                    </h4>
                    <p class="text-xs font-mono text-slate-500 mt-1 leading-relaxed">
                        {{ toastMessage }}
                    </p>
                </div>

                <!-- Close -->
                <button
                    @click="showToast = false"
                    class="text-slate-400 hover:text-slate-600 transition duration-150 flex-shrink-0"
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
