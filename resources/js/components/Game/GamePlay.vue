<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Client } from 'colyseus.js';

interface Player {
    username: string;
    isTyping: boolean;
    lastGuess: string;
    foundAnswer: boolean;
}

interface Props {
    room: any;
}

const props = defineProps<Props>();
const players = ref<Player[]>([]);
const currentGuess = ref<string>('');
const hints = ref<string[]>([]);
const suggestions = ref<string[]>([]);
const isTyping = ref<boolean>(false);
let typingTimeout: NodeJS.Timeout;

function updatePlayers(state: any) {
    players.value = state.players.values().toArray().map((player: any) => ({
        username: player.username,
        isTyping: player.isTyping || false,
        lastGuess: player.lastGuess || '',
        foundAnswer: player.foundAnswer || false
    }));
}

function handleInput() {
    if (!isTyping.value) {
        isTyping.value = true;
        props.room.send('typing_start');
    }

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        isTyping.value = false;
        props.room.send('typing_end');
    }, 1000);

    props.room.send('guess', { guess: currentGuess.value });
}

function handleHint(hint: string) {
    hints.value.push(hint);
}

function handleSuggestions(suggestionsList: string[]) {
    suggestions.value = suggestionsList;
}

function selectSuggestion(suggestion: string) {
    currentGuess.value = suggestion;
    suggestions.value = [];
}

function handlePlayerFoundAnswer(username: string) {
    const player = players.value.find(p => p.username === username);
    if (player) {
        player.foundAnswer = true;
    }
}

function handlePlayerBadGuess(username: string, guess: string) {
    const player = players.value.find(p => p.username === username);
    if (player) {
        player.lastGuess = guess;
    }
}

onMounted(() => {
    props.room.onStateChange((state: any) => {
        updatePlayers(state);
    });

    props.room.onMessage('hint', (hint: string) => {
        handleHint(hint);
    });

    props.room.onMessage('suggestions', (suggestionsList: string[]) => {
        handleSuggestions(suggestionsList);
    });

    props.room.onMessage('player_found_answer', (username: string) => {
        handlePlayerFoundAnswer(username);
    });

    props.room.onMessage('bad_guess', (data: { username: string, guess: string }) => {
        handlePlayerBadGuess(data.username, data.guess);
    });
});
</script>

<template>
    <div class="game-play">
        <div class="players-list">
            <h2 class="title is-4">Joueurs</h2>
            <div v-for="player in players" :key="player.username" class="player-item">
                <span class="player-name">{{ player.username }}</span>
                <span v-if="player.isTyping" class="tag is-info">Écrit...</span>
                <span v-if="player.lastGuess" class="tag is-warning">{{ player.lastGuess }}</span>
                <span v-if="player.foundAnswer" class="tag is-success">Trouvé !</span>
            </div>
        </div>

        <div class="game-content">
            <div class="guess-input">
                <input 
                    type="text" 
                    v-model="currentGuess" 
                    @input="handleInput"
                    class="input is-large"
                    placeholder="Entrez le nom du jeu..."
                >
            </div>

            <div v-if="suggestions.length > 0" class="suggestions">
                <div 
                    v-for="suggestion in suggestions" 
                    :key="suggestion"
                    class="suggestion-item"
                    @click="selectSuggestion(suggestion)"
                >
                    {{ suggestion }}
                </div>
            </div>

            <div class="hints">
                <h3 class="title is-5">Indices</h3>
                <div v-for="(hint, index) in hints" :key="index" class="hint-item">
                    {{ hint }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.game-play {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    height: 100%;
    padding: 2rem;
}

.players-list {
    background-color: var(--gamdle-darker-color);
    padding: 1rem;
    border-radius: 8px;
}

.player-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.game-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.guess-input {
    width: 100%;
}

.guess-input input {
    width: 100%;
    height: 4rem;
    font-size: 1.5rem;
    text-align: center;
}

.suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.suggestion-item {
    padding: 0.5rem 1rem;
    background-color: var(--gamdle-darker-color);
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: var(--bulma-primary);
}

.hints {
    background-color: var(--gamdle-darker-color);
    padding: 1rem;
    border-radius: 8px;
}

.hint-item {
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
</style> 