<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Cpu, Shield, Users, Activity } from 'lucide-vue-next';

import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import admin_routes from '@/routes/admin';
import { type NavItem } from '@/types';
import { computed } from 'vue';

import AppLogo from './AppLogo.vue';

const page = usePage();
const roles = computed(() => {
    const r = page.props.auth.user?.roles || [];
    return Array.isArray(r) ? r.map(role => typeof role === 'string' ? role : role.name) : [];
});
const isAdmin = computed(() => roles.value.includes('admin'));

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
        icon: LayoutGrid,
    },
];

const adminNavItems = computed<NavItem[]>(() => {
    if (!isAdmin.value) return [];
    
    return [
        {
            title: 'Clients',
            href: admin_routes.clients.index().url,
            icon: Cpu,
        },
        {
            title: 'Providers',
            href: admin_routes.providers.index().url,
            icon: Shield,
        },
        {
            title: 'Users',
            href: admin_routes.users.index().url,
            icon: Users,
        },
        {
            title: 'Audit Logs',
            href: admin_routes.audits.index().url,
            icon: Activity,
        },
    ];
});
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard().url">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain v-if="isAdmin" :items="adminNavItems" label="Administration" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
