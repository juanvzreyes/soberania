<script setup>
import { inject } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import Icon from '@/Components/Icon.vue';
import { mdiChevronUp, mdiAccountCircleOutline, mdiLogoutVariant } from '@mdi/js';

const { isCollapsed } = inject('sidebar');
const user = usePage().props.auth.user;
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-full justify-start h-auto p-2">
        <div class="flex items-center w-full">
            <Avatar class="h-8 w-8">
                <AvatarFallback>{{ user.name.charAt(0).toUpperCase() }}</AvatarFallback>
            </Avatar>
            <div v-if="!isCollapsed" class="ml-3 text-left w-full truncate">
                <p class="font-semibold text-sm truncate">{{ user.name }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
            </div>
            <Icon v-if="!isCollapsed" :path="mdiChevronUp" class="h-4 w-4 ml-auto" />
        </div>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-56 mb-2" side="top" align="start">
      <DropdownMenuLabel>{{ user.name }}</DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <Link :href="route('profile.edit')">
            <DropdownMenuItem class="cursor-pointer">
                <Icon :path="mdiAccountCircleOutline" class="mr-2 h-4 w-4" />
                <span>Perfil</span>
            </DropdownMenuItem>
        </Link>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
       <Link :href="route('logout')" method="post" as="button" class="w-full">
            <DropdownMenuItem class="cursor-pointer">
                <Icon :path="mdiLogoutVariant" class="mr-2 h-4 w-4" />
                <span>Cerrar Sesión</span>
            </DropdownMenuItem>
        </Link>
    </DropdownMenuContent>
  </DropdownMenu>
</template>