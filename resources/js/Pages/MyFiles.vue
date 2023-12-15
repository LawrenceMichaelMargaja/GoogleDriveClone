<template>
    <AuthenticatedLayout>
        <nav class="flex items-center justify-between p-1 mb-3">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li v-for="ans of ancestors.data" :key="ans.id" class="inline-flex items-center">
                    <Link v-if="!ans.parent_id" :href="route('myFiles')"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-black"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>

                        <Link :href="route('myFiles', {folder: ans.path})"
                              class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white"
                        >
                            {{ans.name}}
                        </Link>
                    </div>
                </li>
            </ol>
        </nav>
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
import {Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {router} from "@inertiajs/vue3";

// Uses

// Refs

// Props & Emit
const {files} = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object
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
