<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Client } from "colyseus.js";
import UsernameModal from "@/components/modals/UsernameModal.vue";
import CreateGameModal from "@/components/modals/CreateGameModal.vue";
import GamePlay from "@/components/Game/GamePlay.vue";
import { config } from "@/config";

interface Room {
    name: string;
    code: string;
    is_private: boolean;
}

interface User {
    name: string;
    avatar: string;
}

interface Props {
    games: Array<Room>;
    user: User;
    joinOnly: boolean;
    id: string;
}

const props = defineProps<Props>();
const roomId = ref<string>("");
const lobby = ref<boolean>(false);
const joinOnly = ref<boolean>(props.joinOnly);
const id = ref<string>(props.id);
const user = ref<User>(props.user);

const modalActive = ref<boolean>(false);
const createGameModalActive = ref<boolean>(false);
const joinGameModalActive = ref<boolean>(false);
const gameState = ref<'waiting' | 'countdown' | 'playing'>('waiting');
const isHost = ref<boolean>(false);
const isReady = ref<boolean>(false);
const games = ref<Array<Room>>(props.games);
let client: Client;
let room: any;

function openUsernameModal(): void {
    modalActive.value = true;
}

function createGameModal(): void {
    console.log("createGameModal");
    createGameModalActive.value = true;
}

function joinGameModal(): void {
    joinGameModalActive.value = true;
}

function onGameCreated(gameName: string): void {
    client = new Client(config.wsUrl);

    const connectPromise = id.value !== ""
        ? client.joinById(id.value, {username: user.value.name})
        : client.create('game', {username: user.value.name});

    connectPromise
        .then(r => {
            room = r;
            console.log(`Connected to the room ${room.roomId}`);
            roomId.value = `${config.roomUrl}/${room.roomId}`;
            lobby.value = true;
            modalActive.value = false;
            isHost.value = id.value === "";
            // listenEvents(room);
        })
        .catch(error => {
            console.error("Erreur de connexion:", error);
            alert("Une erreur est survenue lors de la connexion. Veuillez réessayer.");
        });
}

function connect(username: string): void {

}

onMounted(() => {
    if (joinOnly.value) {
        openUsernameModal();
    }
});

</script>

<template>
    <Head title="Lobby" />

    <div v-if="gameState === 'playing'">
        <GamePlay :room="room" :games="games" :is-host="isHost" />
    </div>

    <div v-else-if="lobby">

    </div>

    <section class="hero full-height" v-else>
        <UsernameModal :active="modalActive" @username="(username: string) => connect(username)" />
        <CreateGameModal :active="createGameModalActive" @game-created="onGameCreated" />

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
                    <button @click="createGameModal" class="button">
                        Créer une partie
                    </button>

                    <button @click="joinGameModal" class="button">
                        Rejoindre une partie
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
