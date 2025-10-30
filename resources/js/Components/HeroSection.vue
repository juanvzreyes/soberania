<template>
    <div class="relative flex flex-col justify-center items-center p-6 lg:p-10 text-center text-white
             bg-cover bg-center rounded-b-xl lg:rounded-r-xl lg:rounded-b-none
             h-96 lg:h-auto lg:min-h-screen lg:w-1/2" :style="{ backgroundImage: 'url(\'/img/temp/tecnm.jpg\')' }">
        <div class="absolute inset-0 bg-black/65 backdrop-blur-sm rounded-b-xl lg:rounded-r-xl lg:rounded-b-none">
        </div>
        <div
            class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent rounded-b-xl lg:rounded-r-xl lg:rounded-b-none">
        </div>

        <div class="relative z-10 space-y-6">
            <div class="w-32 h-32 lg:w-40 lg:h-40 p-1 mx-auto rounded-full flex items-center justify-center
                   border-2 border-white/50 bg-black/30 shadow-2xl overflow-hidden animate-fade-in">
                <img :src="IMAGES.pivitmorLogoVertical.src" :alt="IMAGES.pivitmorLogoVertical.alt" loading="lazy"
                    class="object-contain w-full h-full p-2" />
            </div>

            <h1
                class="text-3xl lg:text-4xl xl:text-5xl font-extrabold mb-2 text-balance leading-tight drop-shadow-lg animate-slide-in-up">
                TecNM/CENIDET
            </h1>

            <p
                class="text-base lg:text-lg opacity-90 max-w-xs sm:max-w-sm md:max-w-md mx-auto text-pretty leading-relaxed animate-fade-in-delay">
                Formando profesionales líderes, analíticos, críticos y creativos en tecnología e innovación.
            </p>

            <div class="mt-8 flex justify-center space-x-3 animate-pulse-dots">
                <div class="w-2.5 h-2.5 bg-white/70 rounded-full"></div>
                <div class="w-2.5 h-2.5 bg-white/50 rounded-full" style="animation-delay: 0.3s"></div>
                <div class="w-2.5 h-2.5 bg-white/70 rounded-full" style="animation-delay: 0.6s"></div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { IMAGES } from '@/Utils/Image';

</script>
<style scoped>
/* Animaciones y scrollbar (mantener las variables CSS si ya las corregiste) */
@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes slide-in-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

.animate-slide-in-up {
    animation: slide-in-up 0.8s ease-out forwards;
}

.animate-fade-in-delay {
    animation: fade-in 1.2s ease-out 0.3s forwards;
    opacity: 0;
}

.animate-pulse-dots div:nth-child(1) {
    animation: pulse 1.5s infinite ease-in-out;
}

.animate-pulse-dots div:nth-child(2) {
    animation: pulse 1.5s infinite ease-in-out 0.3s;
}

.animate-pulse-dots div:nth-child(3) {
    animation: pulse 1.5s infinite ease-in-out 0.6s;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: var(--tw-bg-gray-100);
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: var(--tw-bg-slate-700);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--tw-bg-blue-300);
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--tw-bg-blue-600);
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--tw-bg-blue-400);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--tw-bg-blue-500);
}
</style>