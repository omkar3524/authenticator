<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Github, Mail, Globe } from 'lucide-vue-next';

defineProps<{
    providers: Array<{
        name: string;
        is_enabled: boolean;
    }>;
    status?: string;
    error?: string;
}>();

const getProviderIcon = (name: string) => {
    switch (name.toLowerCase()) {
        case 'github': return Github;
        case 'microsoft': return Mail;
        case 'google': return Globe;
        default: return Globe;
    }
};
</script>

<template>
    <AuthBase
        title="Welcome Back"
        description="Choose a provider to continue"
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600 border border-green-200 bg-green-50 p-3 rounded-md"
        >
            {{ status }}
        </div>

        <div
            v-if="error"
            class="mb-4 text-center text-sm font-medium text-red-600 border border-red-200 bg-red-50 p-3 rounded-md"
        >
            {{ error }}
        </div>

        <div class="flex flex-col gap-4">
            <template v-for="provider in providers" :key="provider.name">
                <a 
                    :href="route('login.redirect', provider.name)"
                    class="w-full"
                >
                    <Button
                        variant="outline"
                        class="w-full flex items-center justify-center gap-3 py-6 text-base hover:bg-muted transition-colors"
                    >
                        <component :is="getProviderIcon(provider.name)" class="w-5 h-5" />
                        Continue with {{ provider.name.charAt(0).toUpperCase() + provider.name.slice(1) }}
                    </Button>
                </a>
            </template>

            <div v-if="providers.length === 0" class="text-center text-muted-foreground py-8">
                No login providers are currently enabled. Please contact an administrator.
            </div>
        </div>

        <template #footer>
            <div class="space-y-4">
                <div class="text-center text-sm text-muted-foreground">
                    By continuing, you agree to our Terms of Service and Privacy Policy.
                </div>
                <div class="text-center">
                    <Link
                        :href="route('login.admin')"
                        class="text-xs text-muted-foreground hover:text-primary transition-colors underline underline-offset-4"
                    >
                        Sign in as Administrator
                    </Link>
                </div>
            </div>
        </template>
    </AuthBase>
</template>
