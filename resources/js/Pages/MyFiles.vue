<template>
    <AuthenticatedLayout>
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
            <tr>
                <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                    Name
                </th>
                <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                    Owner
                </th>
                <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                    Last Modified
                </th>
                <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                    Size
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100"
                v-for="file of files.data" :key="file.id"
            >
                <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                >
                    {{file.name}}
                </td>
                <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                >
                    {{file.name}}
                </td>
                <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                >
                    {{file.updated_at}}
                </td>
                <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                >
                    {{file.size}}
                </td>
            </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>

<script setup>

// Imports
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Uses

// Refs

// Props & Emit
const {files} = defineProps({
    files: Object
})

// Computed

//Methods

</script>
