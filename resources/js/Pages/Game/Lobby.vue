<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Client } from "colyseus.js";
import Highlight from "@/components/Highlight.vue";
import ConnectedUser from "@/components/ConnectedUser.vue";
import UsernameModal from "@/components/modals/UsernameModal.vue";
import GamePlay from "@/components/Game/GamePlay.vue";

interface Player {
    username: string;
    ready: boolean;
}

interface Props {
    games: Array<any>;
    joinOnly: boolean;
    id: string;
}

const props = defineProps<Props>();
const roomId = ref<string>("");
const lobby = ref<boolean>(false);
const joinOnly = ref<boolean>(props.joinOnly);
const id = ref<string>(props.id);
const players = ref<Player[]>([]);
const modalActive = ref<boolean>(false);
const gameState = ref<'waiting' | 'countdown' | 'playing'>('waiting');
const countdown = ref<number>(0);

let client: Client;
let room: any;

function openUsernameModal(): void {
    modalActive.value = true;
}

function connect(username: string): void {
    client = new Client("ws://localhost:2567");

    const connectPromise = id.value !== "" 
        ? client.joinById(id.value, { username: username })
        : client.create('game', { username: username });

    connectPromise
        .then(r => {
            room = r;
            console.log(`Connected to the room ${room.roomId}`);
            roomId.value = `https://gamdle-royale.test/${room.roomId}`;
            lobby.value = true;
            modalActive.value = false;
            listenEvents(room);
        })
        .catch(error => {
            console.error("Erreur de connexion:", error);
            alert("Une erreur est survenue lors de la connexion. Veuillez réessayer.");
        });
}

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

function startGame(): void {
    room.send('start');
}

onMounted(() => {
    if (joinOnly.value) {
        openUsernameModal();
    }
});

function copyRoomId(): void {
    navigator.clipboard.writeText(roomId.value);
}
</script>

<template>
    <Head title="Lobby" />

    <div v-if="gameState === 'playing'">
        <GamePlay :room="room" />
    </div>

    <div v-else-if="lobby">
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
            <button @click="startGame" class="button is-primary is-large">
                Jouer
            </button>
        </div>
    </div>

    <section class="hero full-height" v-else>
        <UsernameModal :active="modalActive" @username="(username: string) => connect(username)" />

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

                <div class="hero-buttons">
                    <button @click="openUsernameModal" class="button">
                        Jouer
                    </button>
                </div>
            </div>
        </div>
    </section>

</template>

<style>
#app {
    padding: 50px;
}

.full-hero {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
}

.full-height {
    height: calc(100vh - 64px);
}

.hero-body {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.connected-users {
    display: flex;
    flex-direction: column;

    margin-top: 62px;
}

.waiting-message, .countdown-message {
    text-align: center;
    margin-bottom: 2rem;
}

.start-game {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.tag {
    margin-left: 0.5rem;
}
</style>
