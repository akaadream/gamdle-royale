<script setup lang="ts">
defineEmits<{
    (e: 'copyRoomId'): void;
}>();
</script>

<template>
    <p class="highlight-label">Donnez ce lien à vos amis pour les inviter à rejoindre la partie !</p>
    <div class="highlight">
        <slot></slot>

        <button @click="$emit('copyRoomId')" class="button is-danger">
            Copier
        </button>
    </div>
</template>

<style>
.highlight-label {
    padding: 12px 24px;
    font-size: 0.9em;
    font-style: italic;
}
.highlight {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;

    width: 100%;
    padding: 12px 12px 12px 24px;
    border-radius: calc(12px + 8px);

    background-color: var(--gamdle-darker-color);
    outline: 2px solid transparent;

    transition: 200ms;
}

.highlight > button {
    opacity: 0;
    pointer-events: none;
    height: 42px;
    border-radius: 8px;

    transition: 200ms;
}

.highlight:hover {
    outline-color: var(--bulma-danger-on-scheme);
}

.highlight:hover > button {
    opacity: 1;
    pointer-events: auto;
}
</style>
