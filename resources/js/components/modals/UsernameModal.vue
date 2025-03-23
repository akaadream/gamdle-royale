<script setup>
import {ref} from "vue";
import Modal from "@/components/modals/Modal.vue";

const username = ref("");
</script>

<template>
    <Modal>
        <div class="field">
            <label class="label">Nom d'utilisateur</label>
            <div class="control">
                <input ref="username" type="text" class="input" placeholder="Nom d'utilisateur">
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button @click="$emit('username', username)" class="button is-link">Valider</button>
            </div>
        </div>
    </Modal>
</template>

<style>

</style>
