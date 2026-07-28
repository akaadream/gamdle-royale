<script setup lang="ts">
import Modal from "@/components/modals/Modal.vue";
import {ref} from "vue";

interface Props {
    active: boolean;
}

defineProps<Props>();
const gameName = ref<string>("");
const emit = defineEmits<{(e: 'game-created', gameName: string): void}>()

function createGame(): void {
    emit('game-created', gameName.value);
}
</script>

<template>
    <Modal :active="active">
        <div class="field">
            <label class="label">Nom de la partie</label>
            <div class="control">
                <input
                    v-model="gameName"
                    type="text"
                    class="input"
                    placeholder="Nom de la partie"
                    @keydown.enter="createGame"
                >
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button @click="createGame" class="button is-link">Créer</button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>

</style>
