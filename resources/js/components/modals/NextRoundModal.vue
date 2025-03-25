<script setup lang="ts">
import { ref } from "vue";
import Modal from "@/components/modals/Modal.vue";

interface Props {
    active: boolean;
}

const props = defineProps<Props>();

function startNextRound(): void {
    emit('start-next-round');
}

const emit = defineEmits<{
    (e: 'start-next-round'): void;
}>();
</script>

<template>
    <Modal :active="active">
        <div class="content">
            <h3 class="title is-4">Tous les joueurs ont trouvé !</h3>
            <p>Vous êtes l'hôte de la partie. Cliquez sur le bouton ci-dessous pour lancer le prochain round.</p>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button @click="startNextRound" class="button is-primary">Lancer le prochain round</button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.content {
    text-align: center;
    margin-bottom: 2rem;
}
</style> 