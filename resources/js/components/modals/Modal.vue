<script setup>
    import {ref} from "vue";

    const props = defineProps(['active']);
    const isActive = ref(props.active);

    function close() {
        isActive.value = false;
    }

    function onKeyDown(event) {
        if (event.key === "Escape") {
            close();
        }
    }
</script>

<template>
    <div @keydown="onKeyDown" :class="{ 'is-active': isActive }" class="modal">
        <div class="modal-background"></div>
        <div class="modal-content">
            <slot></slot>
        </div>
        <button @click="close" class="modal-close is-large" aria-label="close"></button>
    </div>
</template>

<style>

</style>
