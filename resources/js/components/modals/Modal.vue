<script setup lang="ts">
    import {ref, watch} from "vue";

    interface Props {
        active: boolean;
    }

    const props = defineProps<Props>();
    const isActive = ref<boolean>(props.active);

    watch(() => props.active, (newValue: boolean) => {
        isActive.value = newValue;
    });

    function close(): void {
        isActive.value = false;
    }

    function onKeyDown(event: KeyboardEvent): void {
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
