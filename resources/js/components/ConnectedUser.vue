<script setup>
</script>

<template>
<div class="connected-user">
    <slot></slot>
</div>
</template>

<style>
.connected-user {
    background-color: #242939;
    padding: 12px;
    margin-bottom: 6px;

    outline: 2px solid transparent;
    border-radius: 12px;
    transition: 200ms;
}

.connected-user:hover {
    outline-color: #0a0a0a;
}
</style>
