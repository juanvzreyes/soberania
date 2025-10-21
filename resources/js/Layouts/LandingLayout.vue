<template>
    <div class="relative text-gray-700 dark:text-gray-300">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('/img/bg-agroconecta.jpg'); background-attachment: fixed;">
        </div>
        <div class="absolute inset-0 bg-white/95 dark:bg-gray-950/90 backdrop-blur-sm"></div>

        <header
            class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link :href="route('welcome')" class="flex items-center shrink-0">
                    <h1 class="text-2xl font-twogether font-bold text-emerald-600 dark:text-emerald-400">
                        AgroConecta
                    </h1>
                    </Link>

                    <NavigationMenu class="hidden sm:block">
                        <NavigationMenuList>
                            <template v-for="link in navLinks" :key="link.label">
                                <NavigationMenuItem v-if="link.routeName">
                                    <NavigationMenuLink as-child :active="route().current(link.routeName)">
                                        <Link :href="route(link.routeName)" :class="navigationMenuTriggerStyle()">
                                        {{ link.label }}
                                        </Link>
                                    </NavigationMenuLink>
                                </NavigationMenuItem>
                                <NavigationMenuItem v-if="link.children">
                                    <NavigationMenuTrigger>{{ link.label }}</NavigationMenuTrigger>
                                    <NavigationMenuContent>
                                        <ul class="grid w-[400px] gap-3 p-4 md:w-[500px] lg:w-[600px]">
                                            <ListItem v-for="child in link.children" :key="child.label"
                                                :href="route(child.routeName)" :title="child.label"
                                                :active="route().current(child.routeName)">
                                                {{ child.description }}
                                            </ListItem>
                                        </ul>
                                    </NavigationMenuContent>
                                </NavigationMenuItem>
                            </template>
                        </NavigationMenuList>
                    </NavigationMenu>

                    <nav v-if="canLogin" class="hidden sm:flex items-center space-x-3">
                        <Link v-if="authUser" :href="route('dashboard')"
                            class="px-4 py-2 rounded-md text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition-colors">
                        Mi Panel
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            Iniciar Sesión
                            </Link>
                            <Link v-if="canRegister" :href="route('register')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-full shadow-md text-white bg-emerald-600 hover:bg-emerald-500 transition-all duration-300">
                            Regístrate
                            </Link>
                        </template>
                    </nav>

                    <div class="flex items-center sm:hidden">
                        <Sheet>
                            <SheetTrigger as-child>
                                <Button variant="outline" size="icon" class="shrink-0">
                                    <Menu class="h-5 w-5" />
                                    <span class="sr-only">Abrir menú</span>
                                </Button>
                            </SheetTrigger>
                            <SheetContent>
                                <SheetHeader>
                                    <SheetTitle>
                                        <h1
                                            class="text-2xl font-twogether font-bold text-emerald-600 dark:text-emerald-400">
                                            AgroConecta
                                        </h1>
                                    </SheetTitle>
                                </SheetHeader>

                                <nav class="flex flex-col gap-2 mt-6">
                                    <template v-for="link in navLinks" :key="link.label">
                                        <Link v-if="link.routeName" :href="route(link.routeName)"
                                            class="px-3 py-2 rounded-md text-base font-medium transition-colors" :class="{
                                                'bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300': route().current(link.routeName),
                                                'text-gray-700 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-emerald-900': !route().current(link.routeName)
                                            }">
                                        {{ link.label }}
                                        </Link>
                                        <div v-if="link.children" class="mt-2">
                                            <span
                                                class="block px-3 py-2 text-sm font-semibold text-gray-400 uppercase tracking-wide">{{
                                                    link.label }}</span>
                                            <Link v-for="child in link.children" :key="child.routeName"
                                                :href="route(child.routeName)"
                                                class="flex items-center px-3 py-2 rounded-md text-base font-medium transition-colors"
                                                :class="{
                                                    'bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300': route().current(child.routeName),
                                                    'text-gray-700 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-emerald-900': !route().current(child.routeName)
                                                }">
                                            <span class="ml-4">{{ child.label }}</span>
                                            </Link>
                                        </div>
                                    </template>
                                </nav>

                                <div v-if="canLogin"
                                    class="px-2 pt-2 pb-3 space-y-1 mt-4 border-t border-gray-200/50 dark:border-gray-800">
                                    <Link v-if="authUser" :href="route('dashboard')"
                                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-100">
                                    Mi Panel
                                    </Link>
                                    <template v-else>
                                        <Link :href="route('login')"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-100">
                                        Iniciar Sesión
                                        </Link>
                                        <Link v-if="canRegister" :href="route('register')"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-white bg-emerald-600 hover:bg-emerald-500">
                                        Regístrate
                                        </Link>
                                    </template>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>
                </div>
            </div>
        </header>

        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-emerald-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <slot />
                <footer class="py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                    AgroConecta | Promoviendo la Soberanía Alimentaria en Morelos.
                </footer>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import navLinks from '@/navLinks.js'
import { Menu } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet'
import { NavigationMenu, NavigationMenuContent, NavigationMenuItem, NavigationMenuLink, NavigationMenuList, NavigationMenuTrigger, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu'
import ListItem from '@/Components/ListItem.vue'

const page = usePage();
const canLogin = page.props.canLogin;
const canRegister = page.props.canRegister;
const authUser = page.props.auth.user;
</script>
