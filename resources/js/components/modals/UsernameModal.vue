<script setup lang="ts">
import { ref } from "vue";
import Modal from "@/components/modals/Modal.vue";

interface Props {
    active: boolean;
}

const props = defineProps<Props>();
const username = ref<string>("");

function submitUsername(): void {
    if (username.value.trim()) {
        emit('username', username.value);
        username.value = "";
    }
}

const emit = defineEmits<{
    (e: 'username', username: string): void;
}>();
</script>

<template>
    <Modal :active="active">
        <div class="field">
            <label class="label">Nom d'utilisateur</label>
            <div class="control">
                <input 
                    v-model="username" 
                    type="text" 
                    class="input" 
                    placeholder="Nom d'utilisateur"
                    @keydown.enter="submitUsername"
                >
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button @click="submitUsername" class="button is-link">Valider</button>
            </div>
        </div>
    </Modal>
</template>

<style>

</style>
