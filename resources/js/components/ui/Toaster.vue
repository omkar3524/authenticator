<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { X, CheckCircle, AlertCircle, Info } from 'lucide-vue-next';

const { toasts, dismiss } = useToast();
</script>

<template>
    <div
        class="fixed top-0 right-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"
    >
        <TransitionGroup
            name="toast"
            tag="ol"
            class="flex flex-col gap-2"
        >
            <li
                v-for="toast in toasts"
                :key="toast.id"
                class="group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 shadow-lg transition-all data-[swipe=cancel]:translate-x-0 data-[swipe=end]:translate-x-[var(--radix-toast-swipe-end-x)] data-[swipe=move]:translate-x-[var(--radix-toast-swipe-move-x)] data-[swipe=move]:transition-none"
                :class="{
                    'bg-white text-slate-950 border-slate-200': toast.variant === 'default',
                    'bg-red-600 text-white border-red-600': toast.variant === 'destructive',
                    'bg-green-600 text-white border-green-600': toast.variant === 'success',
                }"
            >
                <div class="flex gap-3">
                    <CheckCircle v-if="toast.variant === 'success'" class="h-5 w-5" />
                    <AlertCircle v-if="toast.variant === 'destructive'" class="h-5 w-5" />
                    <Info v-if="toast.variant === 'default'" class="h-5 w-5 text-blue-500" />
                    
                    <div class="grid gap-1">
                        <div v-if="toast.title" class="text-sm font-semibold">
                            {{ toast.title }}
                        </div>
                        <div v-if="toast.description" class="text-sm opacity-90">
                            {{ toast.description }}
                        </div>
                    </div>
                </div>

                <button
                    @click="dismiss(toast.id)"
                    class="absolute right-2 top-2 rounded-md p-1 opacity-0 transition-opacity focus:opacity-100 focus:outline-none focus:ring-2 group-hover:opacity-100"
                    :class="{
                        'text-slate-950/50 hover:text-slate-950': toast.variant === 'default',
                        'text-red-50 hover:text-red-50': toast.variant === 'destructive',
                         'text-green-50 hover:text-green-50': toast.variant === 'success',
                    }"
                >
                    <X class="h-4 w-4" />
                </button>
            </li>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>
