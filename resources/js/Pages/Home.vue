<script setup lang="ts">
  import {Head} from "@inertiajs/vue3";

  defineProps<{}>()
</script>

<template>
    <Head title="Home" />

    <section class="hero full-height">
        <div class="hero-body">
            <div class="full-hero">
                <div>
                    <p class="title">
                        Gamdle Royale
                    </p>

                    <p class="subtitle">
                        Affrontez vos amis dans ce jeu où le premier à trouver le jeu a gagné !
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
