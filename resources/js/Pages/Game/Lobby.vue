<script setup>
import { Head } from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import {Client, getStateCallbacks} from "colyseus.js";
import Highlight from "@/components/Highlight.vue";
import ConnectedUser from "@/components/ConnectedUser.vue";

const props = defineProps({
    games: Array,
    joinOnly: Boolean,
    id: String
});

const roomId = ref("");
const lobby = ref(false);
const joinOnly = ref(props.joinOnly);
const id = ref(props.id);
const players = ref([]);

let client;

function connect() {
    client = new Client("ws://localhost:2567");

    if (id.value !== "") {
        client.joinById(id.value).then(room => {
            console.log(`Connected to the room ${room.roomId}`);
            roomId.value = `https://gamdle-royale.test/${room.roomId}`;
            lobby.value = true;

            listenEvents(room);
        });
    }
    else {
        client.create('game').then(room => {
            console.log(`Connected to the room ${room.roomId}`);
            roomId.value = `https://gamdle-royale.test/${room.roomId}`;
            lobby.value = true;

            listenEvents(room);
        });
    }
}

function listenEvents(room) {
    room.onStateChange.once((state) => {
        players.value = state.players.values().toArray();
    });

    room.onStateChange((state) => {
        players.value = state.players.values().toArray();
    });
}

onMounted(() => {
    if (joinOnly.value) {
        connect();
    }
});

function copyRoomId() {
    navigator.clipboard.writeText(roomId.value);
}
</script>

<template>
    <Head title="Lobby" />

    <div v-if="lobby">
        <Highlight @copy-room-id="copyRoomId">{{ roomId }}</Highlight>

        <div class="connected-users">
            <div class="subtitle is-3">
                Joueurs en attente
            </div>

            <ConnectedUser v-for="player in players">{{ player.username }}</ConnectedUser>
        </div>
    </div>

    <section class="hero full-height" v-else>
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
                    <button @click="connect" class="button">
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
</style>
