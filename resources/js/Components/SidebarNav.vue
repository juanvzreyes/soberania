<script setup>
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import Icon from '@/Components/Icon.vue';
import { mdiChevronDown } from '@mdi/js';

defineProps({
    menu: {
        type: Array,
        required: true,
    },
});

const isRouteActive = (item) => {
    if (item.route && route().current(item.route)) {
        return true;
    }
    if (item.menu) {
        return item.menu.some(subItem => subItem.route && route().current(subItem.route));
    }
    return false;
};
</script>

<template>
    <nav class="flex flex-col gap-4 px-4 py-6">
        <div v-for="(group, groupIndex) in menu" :key="groupIndex" class="flex flex-col gap-2">
            <h2 v-if="group.labelGroup" class="mb-2 px-4 text-lg font-semibold tracking-tight text-gray-700">
                {{ group.labelGroup }}
            </h2>

            <template v-for="(item, itemIndex) in group.items" :key="itemIndex">
                <Collapsible v-if="item.menu" v-slot="{ open }" class="w-full">
                    <CollapsibleTrigger as-child>
                        <Button :variant="isRouteActive(item) ? 'secondary' : 'ghost'" class="w-full justify-between">
                            <div class="flex items-center gap-3">
                                <Icon v-if="item.icon" :path="item.icon" />
                                <span>{{ item.label }}</span>
                            </div>
                            <Icon :path="mdiChevronDown" class="transition-transform" :class="open && 'rotate-180'" />
                        </Button>
                    </CollapsibleTrigger>
                    <CollapsibleContent class="pl-6 pt-2 space-y-1">
                        <Link v-for="(subItem, subIndex) in item.menu" :key="subIndex" :href="route(subItem.route)">
                        <Button :variant="route().current(subItem.route) ? 'secondary' : 'ghost'"
                            class="w-full justify-start gap-3">
                            <Icon v-if="subItem.icon" :path="subItem.icon" />
                            <span>{{ subItem.label }}</span>
                        </Button>
                        </Link>
                    </CollapsibleContent>
                </Collapsible>

                <Link v-else :href="route(item.route)">
                <Button :variant="route().current(item.route) ? 'secondary' : 'ghost'"
                    class="w-full justify-start gap-3">
                    <Icon v-if="item.icon" :path="item.icon" />
                    <span>{{ item.label }}</span>
                </Button>
                </Link>
            </template>
        </div>
    </nav>
</template>
