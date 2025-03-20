<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({ games: Array });
</script>

<template>
    <Head title="Lobby" />
    <p>{{ games.length }} games loaded</p>
</template>
