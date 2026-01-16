<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import admin from '@/routes/admin';

const props = defineProps<{
    users: {
        data: Array<any>;
        links: Array<any>;
    };
}>();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' },
    { title: 'Users', href: '/admin/users' }
];

const toggleStatus = (user: any) => {
    const form = useForm({
        is_active: !user.is_active,
    });
    form.put(admin.users.update.url(user.id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Manage Users" />

        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">User Management</h1>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>User</TableHead>
                            <TableHead>Social Accounts</TableHead>
                            <TableHead>Roles</TableHead>
                            <TableHead>Active</TableHead>
                            <TableHead>Joined</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ user.name }}</span>
                                    <span class="text-xs text-muted-foreground">{{ user.email }}</span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-1 flex-wrap">
                                    <Badge v-for="acc in user.social_accounts" :key="acc.id" variant="outline" class="capitalize">
                                        {{ acc.provider_name }}
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-1">
                                    <Badge v-for="role in user.roles" :key="role.id">
                                        {{ role.name }}
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Switch 
                                    :model-value="user.is_active" 
                                    @update:modelValue="toggleStatus(user)"
                                />
                            </TableCell>
                            <TableCell class="text-xs text-muted-foreground">
                                {{ new Date(user.created_at).toLocaleDateString() }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
