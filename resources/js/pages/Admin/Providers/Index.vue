<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import admin from '@/routes/admin';

const props = defineProps<{
    providers: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' },
    { title: 'Social Providers', href: '/admin/providers' }
];

const forms = props.providers.reduce((acc, provider) => {
    acc[provider.id] = useForm({
        client_id: provider.client_id || '',
        client_secret: provider.client_secret || '',
        is_enabled: !!provider.is_enabled,
    });
    return acc;
}, {} as Record<number, any>);

const updateProvider = (id: number) => {
    forms[id].put(admin.providers.update(id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Manage Providers" />

        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Social Authentication Providers</h1>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="provider in providers" :key="provider.id">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="capitalize">{{ provider.name }}</CardTitle>
                        <Badge :variant="provider.is_enabled ? 'default' : 'secondary'">
                            {{ provider.is_enabled ? 'Enabled' : 'Disabled' }}
                        </Badge>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="updateProvider(provider.id)" class="space-y-4">
                            <div class="flex items-center justify-between mb-4">
                                <Label :for="'enabled-' + provider.id">Enable Provider</Label>
                                <Switch 
                                    :id="'enabled-' + provider.id" 
                                    v-model="forms[provider.id].is_enabled"
                                    @update:checked="forms[provider.id].is_enabled = $event"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label :for="'client-id-' + provider.id">Client ID</Label>
                                <Input :id="'client-id-' + provider.id" v-model="forms[provider.id].client_id" placeholder="Enter Client ID" />
                            </div>

                            <div class="space-y-2">
                                <Label :for="'client-secret-' + provider.id">Client Secret</Label>
                                <Input :id="'client-secret-' + provider.id" v-model="forms[provider.id].client_secret" type="password" placeholder="••••••••••••" />
                            </div>

                            <Button type="submit" class="w-full" :disabled="forms[provider.id].processing">
                                Save Settings
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
