<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { PlusCircle, Trash2, Pencil } from 'lucide-vue-next';
import admin from '@/routes/admin';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    clients: Array<any>;
    providers: Array<any>;
}>();

const { toast } = useToast();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' },
    { title: 'Clients', href: '/admin/clients' }
];

// Create Form
const createForm = useForm({
    name: '',
    redirect_uris: [''],
    allowed_scopes: [],
});

const addRedirectUri = () => {
    createForm.redirect_uris.push('');
};

const submitCreateClient = () => {
    createForm.post(admin.clients.store.url(), {
        onSuccess: () => {
            createForm.reset();
            toast({
                title: 'Client Created',
                description: 'The client application has been successfully created.',
                variant: 'success',
            });
        },
    });
};

// Edit Form
const editingClient = ref<any>(null);
const isEditOpen = ref(false);

const editForm = useForm({
    name: '',
    redirect_uris: [''],
    allowed_scopes: [],
    is_active: true,
    providers: [] as number[],
});

const openEdit = (client: any) => {
    editingClient.value = client;
    editForm.name = client.name;
    editForm.redirect_uris = [...client.redirect_uris];
    // Ensure we have at least one empty URI field if empty
    if (editForm.redirect_uris.length === 0) editForm.redirect_uris.push('');
    editForm.allowed_scopes = client.allowed_scopes || [];
    editForm.is_active = client.is_active;

    // Map existing providers to IDs
    editForm.providers = client.providers ? client.providers.map((p: any) => p.id) : [];

    isEditOpen.value = true;
};

const addEditRedirectUri = () => {
    editForm.redirect_uris.push('');
};

const submitEditClient = () => {
    if (!editingClient.value) return;
    editForm.put(admin.clients.update.url(editingClient.value.id), {
        onSuccess: () => {
            isEditOpen.value = false;
            toast({
                title: 'Client Updated',
                description: 'The client application has been successfully updated.',
                variant: 'success',
            });
        },
    });
};

const deleteClient = (id: number) => {
    if (confirm('Are you sure you want to delete this client?')) {
        createForm.delete(admin.clients.destroy.url(id), {
            onSuccess: () => {
                toast({
                    title: 'Client Deleted',
                    description: 'The client application has been successfully deleted.',
                    variant: 'success',
                });
            },
        });
    }
};

const toggleProvider = (providerId: number, checked: boolean) => {
    if (checked) {
        if (!editForm.providers.includes(providerId)) {
            editForm.providers.push(providerId);
        }
    } else {
        editForm.providers = editForm.providers.filter(id => id !== providerId);
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
                                        <div class="flex items-center gap-2">
                                            <Button variant="ghost" size="icon" @click="openEdit(client)">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                            <Button variant="ghost" size="icon" @click="deleteClient(client.id)">
                                                <Trash2 class="h-4 w-4 text-red-500" />
                                            </Button>
                                        </div>
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
                        <form @submit.prevent="submitCreateClient" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">App Name</Label>
                                <Input id="name" v-model="createForm.name" placeholder="E.g. My Website" required />
                            </div>
                            <div class="space-y-2">
                                <Label>Redirect URIs</Label>
                                <div v-for="(uri, index) in createForm.redirect_uris" :key="index" class="flex gap-2 mb-2">
                                    <Input v-model="createForm.redirect_uris[index]" placeholder="https://example.com/callback" required />
                                </div>
                                <Button type="button" variant="outline" size="sm" @click="addRedirectUri" class="w-full">
                                    <PlusCircle class="mr-2 h-4 w-4" /> Add URI
                                </Button>
                            </div>
                            <Button type="submit" class="w-full" :disabled="createForm.processing">
                                Create Client
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>

        <Dialog v-model:open="isEditOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Edit Client</DialogTitle>
                    <DialogDescription>
                        Make changes to the client application and its settings.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitEditClient" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="edit-name">App Name</Label>
                        <Input id="edit-name" v-model="editForm.name" required />
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Redirect URIs</Label>
                        <div v-for="(uri, index) in editForm.redirect_uris" :key="index" class="flex gap-2 mb-2">
                            <Input v-model="editForm.redirect_uris[index]" required />
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="addEditRedirectUri" class="w-full">
                            <PlusCircle class="mr-2 h-4 w-4" /> Add URI
                        </Button>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox id="is_active" :model-value="editForm.is_active" @update:modelValue="(v) => editForm.is_active = v" />
                        <Label for="is_active">Client Active</Label>
                    </div>

                    <div class="space-y-2">
                        <Label>Enabled Identity Providers</Label>
                        <div v-for="provider in providers" :key="provider.id" class="flex items-center space-x-2">
                            <Checkbox 
                                :id="`provider-${provider.id}`" 
                                :model-value="editForm.providers.includes(provider.id)"
                                @update:modelValue="(checked) => toggleProvider(provider.id, checked as boolean)"
                            />
                            <Label :for="`provider-${provider.id}`">{{ provider.name }}</Label>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="submit" :disabled="editForm.processing">Save Changes</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
