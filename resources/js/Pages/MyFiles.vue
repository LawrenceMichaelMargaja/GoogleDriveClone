<template>
    <AuthenticatedLayout>
        <pre>{{ancestors}}</pre>
        <table v-if="files.data.length" class="min-w-full">
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
                @dblclick="openFolder(file)"
                class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 cursor-pointer"
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
                    {{file.owner}}
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
        <div v-else class="py-8 text-center text-lg text-gray-400">
            There is no data in this folder
        </div>
    </AuthenticatedLayout>
</template>

<script setup>

// Imports
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {router} from "@inertiajs/vue3";

// Uses

// Refs

// Props & Emit
const {files} = defineProps({
    files: Object,
    folder: Object,
    ancestors: Array
})

// Computed

//Methods
function openFolder(file) {
    console.log("the file --- ", file)
    console.log("file.path} --- ", file.path)
    if(!file.is_folder) {
        return;
    }

    /**
     * Point of interest:
     * This seems to be where you're passing the props, and I think file.path is
     * the current folder open (could be null, or could be not)
     */
    router.visit(route('myFiles', {folder: file.path}))
}

</script>
