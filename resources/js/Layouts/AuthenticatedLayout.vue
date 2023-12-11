<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation/>

        <main @drop.prevent="handleDrop"
              @dragover="onDragOver"
              @dragleave.prevent="onDragLeave()"
              class="flex flex-col flex-1 px-4 overflow-hidden"
              :class="dragOver ? 'dropzone' : ''"
        >
            <template v-if="dragOver" class="text-gray-400 text-center py-0 text-sm">
                Drop Files Here To Upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm/>
                    <UserSettingDropdown/>
                </div>
                <div class="flex-1 flex flex-col overflow-hidden">
                    <slot/>
                </div>
            </template>
        </main>
    </div>
</template>

<script setup>
    // Imports
    import Navigation from "@/Components/app/Navigation.vue";
    import SearchForm from "@/Components/app/SearchForm.vue";
    import UserSettingDropdown from "@/Components/app/UserSettingDropdown.vue";
    import {handleError, onMounted, ref} from "vue";
    import {emitter, FILE_UPLOAD_STARTED} from "@/event-bus.js";
    import {useForm, usePage} from "@inertiajs/vue3";

    // Uses
    const page = usePage();
    const fileUploadForm = useForm({
        files: [],
        parent_id: null
    })

    // Refs
    const dragOver = ref(false)

    // Props and Emit

    // Computed

    // Methods
    function onDragOver () {
        dragOver.value = true
    }

    function onDragLeave () {
        dragOver.value = false
    }

    function handleDrop (ev) {
        dragOver.value = false;
        const files = ev.dataTransfer.files

        if(!files.length) {
            return
        }

        uploadFiles(files)
    }

    function uploadFiles(files) {
        // console.log(files);
        fileUploadForm.parent_id = page.props.folder.id
        fileUploadForm.files = files

        fileUploadForm.post(route('file.store'))
     }

    // Hooks
    onMounted(() => {
        emitter.on(FILE_UPLOAD_STARTED, uploadFiles)
    })
</script>

<style scoped>
    .dropzone {
        width: 100%;
        height: 100%;
        color: #8d8d8d;
        border: 2px dashed grey;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
