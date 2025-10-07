<script setup>
// 1. Import 'ref' and 'watch' from Vue and 'usePage' from Inertia
import { inject, ref, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import Icon from '@/Components/Icon.vue';
import { mdiChevronDown } from '@mdi/js';

const { isCollapsed } = inject('sidebar');
const props = defineProps({ item: Object });

const isRouteActive = (item) => {
  if (item.route && route().current(item.route)) return true;
  if (item.menu) return item.menu.some(sub => sub.route && route().current(sub.route));
  return false;
};

// 2. Create a state variable to control the collapsible's open state
const isCollapsibleOpen = ref(false);

// 3. Watch for changes in the page URL
watch(() => usePage().url, () => {
    // If the current route is a child of this item, open the collapsible
    if (isRouteActive(props.item)) {
        isCollapsibleOpen.value = true;
    }
}, { immediate: true }); // 'immediate: true' ensures this runs on initial load

</script>

<template>
  <TooltipProvider :delay-duration="100">
    <Tooltip>
      <Link v-if="item.route" :href="route(item.route)" as="div">
        <TooltipTrigger as-child>
          <Button :variant="isRouteActive(item) ? 'secondary' : 'ghost'" class="w-full justify-start h-10" :class="isCollapsed && 'justify-center p-2'">
            <Icon v-if="item.icon" :path="item.icon" class="h-5 w-5" />
            <span v-if="!isCollapsed" class="ml-4 truncate">{{ item.label }}</span>
          </Button>
        </TooltipTrigger>
        <TooltipContent v-if="isCollapsed" side="right">{{ item.label }}</TooltipContent>
      </Link>

      <Collapsible v-else-if="item.menu" v-model:open="isCollapsibleOpen" class="w-full">
        <TooltipTrigger as-child>
          <CollapsibleTrigger as-child>
            <Button :variant="isRouteActive(item) ? 'secondary' : 'ghost'" class="w-full justify-between h-10" :class="isCollapsed && 'justify-center p-2'">
              <div class="flex items-center">
                <Icon v-if="item.icon" :path="item.icon" class="h-5 w-5" />
                <span v-if="!isCollapsed" class="ml-4 truncate">{{ item.label }}</span>
              </div>
              <Icon v-if="!isCollapsed" :path="mdiChevronDown" class="h-4 w-4 shrink-0 transition-transform duration-200" :class="isCollapsibleOpen && 'rotate-180'" />
            </Button>
          </CollapsibleTrigger>
        </TooltipTrigger>
        <TooltipContent v-if="isCollapsed" side="right">{{ item.label }}</TooltipContent>
        <CollapsibleContent v-if="!isCollapsed" class="pl-6 pt-1 space-y-1">
          <Link v-for="(sub, i) in item.menu" :key="i" :href="route(sub.route)">
            <Button :variant="route().current(sub.route) ? 'secondary' : 'ghost'" class="w-full justify-start h-9 gap-3">
              <Icon v-if="sub.icon" :path="sub.icon" />
              <span>{{ sub.label }}</span>
            </Button>
          </Link>
        </CollapsibleContent>
      </Collapsible>
    </Tooltip>
  </TooltipProvider>
</template>