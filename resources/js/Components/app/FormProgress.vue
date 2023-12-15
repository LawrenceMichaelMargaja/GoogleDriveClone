<template>
    <div
        v-if="form.progress"
        class="absolute border-gray-900 border right-8 bottom-4 bg-white shadow-lg rounded flex flex-col w-[240px] p-3"
    >
        <div class="text-xs mb-2">
            Uploading {{form.files.length}} files
            <div class="h-3 bg-gray-100 rounded-md">
                <div class="h-full bg-gray-800 rounded-md transition-all" :style="{width: form.progress.percentage+'%'}">

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Import

// Uses

// Refs

// Props & Emit
const props = defineProps({
    form: Object
})

// Computed

// Methods

// Hooks


</script>

<style scoped>

</style>
