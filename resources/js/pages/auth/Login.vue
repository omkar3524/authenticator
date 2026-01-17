<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';


const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase
        title="Login"
        description="Sign in to your account"
    >
        <Head title="Login" />

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="user@example.com"
                />
                <div v-if="form.errors.email" class="text-sm text-red-600">
                    {{ form.errors.email }}
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Password</Label>
                    <Link
                        href="/forgot-password"
                        class="text-sm font-medium text-primary hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>
                <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    placeholder="••••••••"
                />
                <div v-if="form.errors.password" class="text-sm text-red-600">
                    {{ form.errors.password }}
                </div>
            </div>

            <Button
                type="submit"
                class="w-full"
                :disabled="form.processing"
            >
                Sign In
            </Button>
        </form>

        <template #footer>
            <div class="text-center">
                <Link
                    href="/auth"
                    class="text-sm text-muted-foreground hover:text-primary transition-colors"
                >
                    &larr; Login with another provider
                </Link>
            </div>
        </template>
    </AuthBase>
</template>
