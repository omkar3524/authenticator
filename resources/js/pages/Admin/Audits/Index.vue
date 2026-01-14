<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

defineProps<{
    audits: {
        data: Array<any>;
        links: Array<any>;
    };
}>();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' },
    { title: 'Audit Logs', href: '/admin/audits' }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Audit Logs" />

        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Login Audit Logs</h1>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>User</TableHead>
                            <TableHead>Client ID</TableHead>
                            <TableHead>IP Address</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Details</TableHead>
                            <TableHead>Timestamp</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="audit in audits.data" :key="audit.id">
                            <TableCell>
                                {{ audit.user ? audit.user.name : 'Guest' }}
                                <div class="text-xs text-muted-foreground" v-if="audit.user">{{ audit.user.email }}</div>
                            </TableCell>
                            <TableCell class="font-mono text-xs">{{ audit.client_id || 'Internal' }}</TableCell>
                            <TableCell class="text-xs">{{ audit.ip_address }}</TableCell>
                            <TableCell>
                                <Badge :variant="audit.status === 'success' ? 'default' : 'destructive'">
                                    {{ audit.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="max-w-xs truncate text-xs">{{ audit.details }}</TableCell>
                            <TableCell class="text-xs text-muted-foreground whitespace-nowrap">
                                {{ new Date(audit.created_at).toLocaleString() }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="audits.data.length === 0">
                            <TableCell colspan="6" class="text-center py-4 text-muted-foreground">
                                No audit logs found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
