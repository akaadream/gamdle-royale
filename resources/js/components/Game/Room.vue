<script setup lang="ts">

import ConnectedUser from "@/components/ConnectedUser.vue";
import Highlight from "@/components/Highlight.vue";
import {ref} from "vue";

interface Player {
    username: string;
    ready: boolean;
}

const roomId = ref<string>("");
const gameState = ref<'waiting' | 'countdown' | 'playing'>('waiting');
const countdown = ref<number>(0);
const isHost = ref<boolean>(false);
const isReady = ref<boolean>(false);
const players = ref<Player[]>([]);

let room: any;

function listenEvents(room: any): void {
    room.onStateChange.once((state: any) => {
        players.value = state.players.values().toArray();
    });

    room.onStateChange((state: any) => {
        players.value = state.players.values().toArray();
        gameState.value = state.gameState;
        countdown.value = state.countdown;
    });
}

function copyRoomId(): void {
    navigator.clipboard.writeText(roomId.value);
}

function startGame(): void {
    room.send('start');
}

function toggleReady(): void {
    room.send('toggle_ready');
    isReady.value = !isReady.value;
}
</script>

<template>
    <div v-if="gameState === 'waiting'" class="waiting-message">
        <p class="title is-1">En attente des autres joueurs</p>
    </div>

    <div v-if="gameState === 'countdown'" class="countdown-message">
        <p class="title is-1">{{ countdown }}</p>
    </div>

    <Highlight @copy-room-id="copyRoomId">{{ roomId }}</Highlight>

    <div class="connected-users">
        <div class="subtitle is-3">
            Joueurs en attente
        </div>

        <ConnectedUser v-for="player in players" :key="player.username">
            {{ player.username }}
            <span v-if="player.ready" class="tag is-success">Prêt</span>
        </ConnectedUser>
    </div>

    <div class="start-game">
        <button v-if="isHost" @click="startGame" class="button is-primary is-large">
            Lancer la partie
        </button>

        <button @click="toggleReady" class="button is-primary is-large">
            <span v-if="isReady">Pas prêt</span>
            <span v-else>Prêt</span>
        </button>
    </div>
</template>

<style scoped>

</style>
