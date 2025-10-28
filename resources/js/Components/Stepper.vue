<template>
    <div class="w-full py-6">
        <div class="flex items-center justify-between">
            <div v-for="(step, index) in steps" :key="index" class="flex-1 flex items-center">
                <div class="flex flex-col items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all"
                        :class="getStepClasses(index)">
                        <span v-if="index < currentStep" class="text-white font-bold">✓</span>
                        <span v-else class="font-semibold">{{ index + 1 }}</span>
                    </div>
                    <span class="mt-2 text-sm font-medium" 
                        :class="index <= currentStep ? 'text-forest-600 dark:text-forest-400' : 'text-gray-400'">
                        {{ step }}
                    </span>
                </div>
                <div v-if="index < steps.length - 1" 
                    class="flex-1 h-1 mx-2 transition-all"
                    :class="index < currentStep ? 'bg-forest-600' : 'bg-gray-300 dark:bg-gray-600'">
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    steps: {
        type: Array,
        required: true
    },
    currentStep: {
        type: Number,
        required: true
    }
});

const getStepClasses = (index) => {
    if (index < props.currentStep) {
        return 'bg-white dark:bg-slate-800 border-forest-600 text-forest-600 dark:text-forest-400';
    } else if (index === props.currentStep) {
        return 'bg-white dark:bg-slate-800 border-forest-600 text-forest-600 dark:text-forest-400';
    } else {
        return 'bg-white dark:bg-slate-800 border-gray-300 dark:border-gray-600 text-gray-400';
    }
};
</script>