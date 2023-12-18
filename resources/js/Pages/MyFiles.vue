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
            <div>
                <DownloadFileButton :all="allSelected" :ids="selectedIds" class="mr-2" />
                <DeleteFilesButton :delete-all="allSelected" :delete-ids="selectedIds" @delete="onDelete"/>
            </div>
        </nav>
<!--        <pre>{{selectedIds}}</pre>-->
        <div class="flex-1 overflow-auto">
<!--            <pre>{{allSelected}}</pre>-->
<!--            <pre>{{selected}}</pre>-->
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left w-[30px] max-w-[30px] pr-0">
                        <Checkbox @change="onSelectAllChange" v-model:checked="allSelected"/>
                    </th>
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
                    class="bg-white border-b transition duration-300 ease-in-out hover:bg-blue-100 cursor-pointer"
                    v-for="file of allFiles.data" :key="file.id"
                    @click="$event => toggleFileSelect(file)"
                    :class="(selected[file.id] || allSelected) ? 'bg-blue-50' : 'bg-white' "
                >
                    <!--                {{JSON.stringify(files.data)}}-->
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium w-[30px] max-w-[30px] pr-0"
                    >
                        <Checkbox @change="$event => onSelectCheckboxChange(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected"/>
                    </td>
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center"
                    >
                        <FileIcon :file="file"/>
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
            <div v-if="!allFiles.data.length"  class="py-8 text-center text-lg text-gray-400">
                There is no data in this folder
            </div>
            <div ref="loadMoreIntersect"></div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>

// Imports
import {Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {router} from "@inertiajs/vue3";
import FileIcon from "@/Components/app/FileIcon.vue";
import {computed, onMounted, onUpdated, ref} from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import {httpGet} from "@/Helper/http-helper.js";
import DeleteFilesButton from "@/Components/app/DeleteFilesButton.vue";
import DownloadFileButton from "@/Components/app/DownloadFileButton.vue";

// Uses


// Props & Emit
const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object
})

// Refs
const allSelected = ref(false);
const selected = ref({});
const loadMoreIntersect = ref(null)

const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next
})

// Computed
const selectedIds = computed(() => Object.entries(selected.value).filter(a => a[1]).map(a => a[0]))

//Methods
function openFolder(file) {
    // console.log("the file --- ", file)
    // console.log("file.path} --- ", file.path)
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

function onSelectAllChange() {
    allFiles.value.data.forEach(f => {
        selected.value[f.id] = allSelected.value
    })
}

function toggleFileSelect(file) {
    selected.value[file.id] = !selected.value[file.id]
    onSelectCheckboxChange(file)
}

function loadMore() {
    console.log("load more")
    console.log(allFiles.value.next);

    if (allFiles.value.next === null) {
        return
    }

    httpGet(allFiles.value.next)
        .then(res => {
            allFiles.value.data = [...allFiles.value.data, ...res.data]
            allFiles.value.next = res.links.next
        })
}

function onSelectCheckboxChange(file) {
    if(!selected.value[file.id]) {
        allSelected.value = false;
    } else {
        let checked = true;

        for(let file of allFiles.value.data) {
            if(!selected.value[file.id]) {
                checked = false;
                break;
            }
        }

        allSelected.value = checked;
    }
}

function onDelete() {
    allSelected.value = false
    selected.value = {}
}


// Hooks
onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next
    }
})

onMounted(() => {
    // allFiles.value = {
    //     data: props.files.data,
    //     next: props.files.links.next
    // }
    // console.log('the files 4 --- ', files.data);
    const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
        rootMargin: '-250px 0px 0px 0px'
    })

    observer.observe(loadMoreIntersect.value)
});

</script>
