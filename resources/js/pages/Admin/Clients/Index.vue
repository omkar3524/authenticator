<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { PlusCircle, Trash2 } from 'lucide-vue-next';

defineProps<{
    clients: Array<any>;
}>();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' },
    { title: 'Clients', href: '/admin/clients' }
];

const form = useForm({
    name: '',
    redirect_uris: [''],
    allowed_scopes: [],
});

const addRedirectUri = () => {
    form.redirect_uris.push('');
};

const submitClient = () => {
    form.post(route('admin.clients.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteClient = (id: number) => {
    if (confirm('Are you sure you want to delete this client?')) {
        form.delete(route('admin.clients.destroy', id));
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Manage Clients" />

        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Client Applications</h1>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card class="col-span-2">
                    <CardHeader>
                        <CardTitle>Registered Clients</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Client ID</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="client in clients" :key="client.id">
                                    <TableCell class="font-medium">{{ client.name }}</TableCell>
                                    <TableCell class="font-mono text-xs">{{ client.client_id }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="client.is_active ? 'default' : 'secondary'">
                                            {{ client.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Button variant="ghost" size="icon" @click="deleteClient(client.id)">
                                            <Trash2 class="h-4 w-4 text-red-500" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="clients.length === 0">
                                    <TableCell colspan="4" class="text-center py-4 text-muted-foreground">
                                        No clients found.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Register New Client</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submitClient" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">App Name</Label>
                                <Input id="name" v-model="form.name" placeholder="E.g. My Website" required />
                            </div>
                            <div class="space-y-2">
                                <Label>Redirect URIs</Label>
                                <div v-for="(uri, index) in form.redirect_uris" :key="index" class="flex gap-2 mb-2">
                                    <Input v-model="form.redirect_uris[index]" placeholder="https://example.com/callback" required />
                                </div>
                                <Button type="button" variant="outline" size="sm" @click="addRedirectUri" class="w-full">
                                    <PlusCircle class="mr-2 h-4 w-4" /> Add URI
                                </Button>
                            </div>
                            <Button type="submit" class="w-full" :disabled="form.processing">
                                Create Client
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
