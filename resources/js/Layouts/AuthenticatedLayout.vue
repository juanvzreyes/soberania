<script setup>
import { ref, provide, readonly, computed } from 'vue';
import { useAside } from '@/Layouts/Composables/asideMenu.js';
import {
    Sidebar,
    SidebarHeader,
    SidebarContent,
    SidebarFooter,
    SidebarTrigger,
    SidebarUserMenu
} from '@/Components/Sidebar';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import Icon from '@/Components/Icon.vue';
import { mdiMenu, mdiCartOutline } from '@mdi/js';
import NotificationBell from '@/Components/NotificationBell.vue';
defineProps({
    title: String,
});

const isCollapsed = ref(false);
function toggleCollapse() {
    isCollapsed.value = !isCollapsed.value;
}
provide('sidebar', {
    isCollapsed: readonly(isCollapsed),
    toggleCollapse,
});

const { asideMenu } = useAside();

const mainContentClasses = computed(() => {
    return isCollapsed.value ? 'md:pl-20' : 'md:pl-64';
});
</script>

<template>
    <div class="min-h-screen w-full bg-gray-100">
        <Sidebar>
            <SidebarHeader />
            <SidebarContent :menu="asideMenu" />
            <SidebarFooter>
                <SidebarUserMenu />
            </SidebarFooter>
        </Sidebar>

        <div :class="['flex flex-col transition-all duration-300 ease-in-out', mainContentClasses]">
            <header class="flex h-16 items-center gap-4 px-4 md:px-6 shrink-0">
                <SidebarTrigger />
                <div class="flex-1">
                    <h1 v-if="title" class="text-lg font-semibold text-gray-900">
                        {{ title }}
                    </h1>
                </div>
                <div class="hidden md:flex items-center gap-2 mt-4">
                    <NotificationBell />
                </div>

                <div class="md:hidden flex items-center gap-2">
                    <NotificationBell />

                    <Sheet>
                        <SheetTrigger as-child>
                            <Button variant="outline" size="icon" class="shrink-0">
                                <Icon :path="mdiMenu" size="24" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="p-0 flex flex-col w-64">
                            <SidebarHeader />
                            <SidebarContent :menu="asideMenu" />
                            <SidebarFooter>
                                <SidebarUserMenu />
                            </SidebarFooter>
                        </SheetContent>
                    </Sheet>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
