<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Client } from 'colyseus.js';
import NextRoundModal from '@/components/modals/NextRoundModal.vue';

interface Player {
    username: string;
    isTyping: boolean;
    lastGuess: string;
    foundAnswer: boolean;
}

interface Props {
    room: any;
    games: Array<{ name: string }>;
    isHost: boolean;
}

const props = defineProps<Props>();
const players = ref<Player[]>([]);
const currentGuess = ref<string>('');
const hints = ref<string[]>([]);
const suggestions = ref<string[]>([]);
const selectedSuggestionIndex = ref<number>(-1);
const isTyping = ref<boolean>(false);
const lastGuessResult = ref<'good' | 'bad' | null>(null);
const hasFoundAnswer = ref<boolean>(false);
const showNextRoundModal = ref<boolean>(false);
const roundTime = ref<number>(90);
const roundNumber = ref<number>(0);
const nextHintTime = ref<number>(0);
let typingTimeout: NodeJS.Timeout;
let roundTimer: NodeJS.Timeout;
let hintTimer: NodeJS.Timeout;

// Fonction pour calculer la distance de Levenshtein
function levenshteinDistance(a: string, b: string): number {
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;

    const matrix = Array(b.length + 1).fill(null).map(() => Array(a.length + 1).fill(null));

    for (let i = 0; i <= a.length; i++) matrix[0][i] = i;
    for (let j = 0; j <= b.length; j++) matrix[j][0] = j;

    for (let j = 1; j <= b.length; j++) {
        for (let i = 1; i <= a.length; i++) {
            const cost = a[i - 1] === b[j - 1] ? 0 : 1;
            matrix[j][i] = Math.min(
                matrix[j][i - 1] + 1,
                matrix[j - 1][i] + 1,
                matrix[j - 1][i - 1] + cost
            );
        }
    }

    return matrix[b.length][a.length];
}

// Fonction pour calculer la similarité entre deux chaînes
function calculateSimilarity(str1: string, str2: string): number {
    if (str2.includes(str1)) {
        return 1;
    }

    const maxLength = Math.max(str1.length, str2.length);
    const distance = levenshteinDistance(str1.toLowerCase(), str2.toLowerCase());
    return 1 - (distance / maxLength);
}

function updatePlayers(state: any) {
    players.value = state.players.values().toArray().map((player: any) => ({
        username: player.username,
        isTyping: player.isTyping || false,
        lastGuess: player.lastGuess || '',
        foundAnswer: player.foundAnswer || false
    }));

    // Vérifier si tous les joueurs ont trouvé la réponse
    const allFound = players.value.every(player => player.foundAnswer);
    if (allFound && props.isHost) {
        showNextRoundModal.value = true;
    }
}

function handleInput() {
    if (!isTyping.value) {
        isTyping.value = true;
        props.room.send('typing_start');
    }

    if (typingTimeout) {
        clearTimeout(typingTimeout);
    }

    typingTimeout = setTimeout(() => {
        isTyping.value = false;
        props.room.send('typing_end');
    }, 1000);

    props.room.send('guess', { guess: currentGuess.value });

    // Filtrer les suggestions avec une approche plus intelligente
    if (currentGuess.value.trim()) {
        const searchTerm = currentGuess.value.toLowerCase().trim();
        const threshold = 0.5; // Seuil de similarité minimum

        suggestions.value = props.games
            .map(game => ({
                name: game.name,
                similarity: calculateSimilarity(searchTerm, game.name.toLowerCase())
            }))
            .filter(game => game.similarity >= threshold)
            .sort((a, b) => b.similarity - a.similarity)
            .map(game => game.name)
            .slice(0, 5);

        selectedSuggestionIndex.value = -1;
    } else {
        suggestions.value = [];
    }
}

function handleKeydown(event: KeyboardEvent) {
    if (suggestions.value.length === 0) return;

    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedSuggestionIndex.value = (selectedSuggestionIndex.value + 1) % suggestions.value.length;
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedSuggestionIndex.value = selectedSuggestionIndex.value <= 0 
                ? suggestions.value.length - 1 
                : selectedSuggestionIndex.value - 1;
            break;
        case 'Enter':
            event.preventDefault();
            if (selectedSuggestionIndex.value >= 0) {
                const selectedSuggestion = suggestions.value[selectedSuggestionIndex.value];
                currentGuess.value = selectedSuggestion;
                suggestions.value = [];
                selectedSuggestionIndex.value = -1;
                // Envoyer le message guess au serveur
                props.room.send('guess', { guess: selectedSuggestion });
                props.room.send('attempt', { game_name: selectedSuggestion });
                currentGuess.value = '';
            } else {
                handleSubmit(event);
            }
            break;
    }
}

function handleSubmit(event: KeyboardEvent) {
    if (event.key === 'Enter' && currentGuess.value.trim()) {
        props.room.send('attempt', { game_name: currentGuess.value.trim() });
        currentGuess.value = '';
        suggestions.value = [];
    }
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
    selectedSuggestionIndex.value = -1;
    // Envoyer le message guess au serveur
    props.room.send('guess', { guess: suggestion });
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

function handleGoodAnswer() {
    lastGuessResult.value = 'good';
    hasFoundAnswer.value = true;
    setTimeout(() => {
        lastGuessResult.value = null;
    }, 2000);
}

function handleBadAnswer() {
    lastGuessResult.value = 'bad';
    setTimeout(() => {
        lastGuessResult.value = null;
    }, 2000);
}

function startNextRound() {
    props.room.send('start-next-round');
    showNextRoundModal.value = false;
}

function updateRoundInfo(state: any) {
    roundNumber.value = state.roundNumber;
    roundTime.value = 90 - state.roundTime;
    nextHintTime.value = (90 - state.roundTime) % 10;
}

function formatTime(seconds: number): string {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function startRoundTimers() {
    if (roundTimer) clearInterval(roundTimer);
    if (hintTimer) clearInterval(hintTimer);

    roundTimer = setInterval(() => {
        if (roundTime.value > 0) {
            roundTime.value--;
        } else {
            clearInterval(roundTimer);
            props.room.send('round-timeout');
        }
    }, 1000);

    hintTimer = setInterval(() => {
        if (nextHintTime.value > 0) {
            nextHintTime.value--;
        } else {
            clearInterval(hintTimer);
        }
    }, 1000);
}

onMounted(() => {
    props.room.onStateChange((state: any) => {
        updatePlayers(state);
        updateRoundInfo(state);
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

    props.room.onMessage('good_answer', () => {
        handleGoodAnswer();
    });

    props.room.onMessage('bad_answer', () => {
        handleBadAnswer();
    });

    props.room.onMessage('player_joined', (username: string) => {
        const newPlayer: Player = {
            username,
            isTyping: false,
            lastGuess: '',
            foundAnswer: false
        };
        players.value.push(newPlayer);
    });

    props.room.onMessage('player_left', (username: string) => {
        const index = players.value.findIndex(p => p.username === username);
        if (index !== -1) {
            players.value.splice(index, 1);
        }
    });

    props.room.onMessage('player_typing', (username: string) => {
        const player = players.value.find(p => p.username === username);
        if (player) {
            player.isTyping = true;
        }
    });

    props.room.onMessage('player_stop_typing', (username: string) => {
        const player = players.value.find(p => p.username === username);
        if (player) {
            player.isTyping = false;
        }
    });

    props.room.onMessage('round-start', () => {
        startRoundTimers();
    });

    props.room.onMessage('round-end', () => {
        clearInterval(roundTimer);
        clearInterval(hintTimer);
    });
});

onUnmounted(() => {
    clearInterval(roundTimer);
    clearInterval(hintTimer);
});
</script>

<template>
    <div class="game-play">
        <div class="left-side">
            <div class="round-info">
                <span class="tag is-big is-primary">Round {{ roundNumber + 1 }}/10</span>
                <span class="tag is-big is-info">Temps restant : {{ formatTime(roundTime) }}</span>
                <span class="tag is-big is-warning">Prochain indice dans : {{ formatTime(nextHintTime) }}</span>
            </div>

            <div class="players-list">
                <h2 class="title is-4">Joueurs</h2>
                <div v-for="player in players" :key="player.username" class="player-item">
                    <span class="player-name">{{ player.username }}</span>
                    <span v-if="player.isTyping" class="tag is-info">Écrit...</span>
                    <span v-if="player.lastGuess" class="tag is-warning">{{ player.lastGuess }}</span>
                    <span v-if="player.foundAnswer" class="tag is-success">Trouvé !</span>
                </div>
            </div>
        </div>

        <div class="game-content">
            <div v-if="!hasFoundAnswer" class="guess-input">
                <input 
                    type="text" 
                    v-model="currentGuess" 
                    @input="handleInput"
                    @keydown="handleKeydown"
                    class="input is-large"
                    :class="{
                        'is-success': lastGuessResult === 'good',
                        'is-danger': lastGuessResult === 'bad'
                    }"
                    placeholder="Entrez le nom du jeu..."
                >
                <div v-if="lastGuessResult === 'good'" class="notification is-success">
                    Bonne réponse !
                </div>
                <div v-if="lastGuessResult === 'bad'" class="notification is-danger">
                    Mauvaise réponse, essayez encore !
                </div>
            </div>

            <div v-if="suggestions.length > 0 && !hasFoundAnswer" class="suggestions">
                <div 
                    v-for="(suggestion, index) in suggestions" 
                    :key="suggestion"
                    class="suggestion-item"
                    :class="{ 'is-selected': index === selectedSuggestionIndex }"
                    @click="selectSuggestion(suggestion)"
                >
                    {{ suggestion }}
                </div>
            </div>

            <div v-if="!hasFoundAnswer" class="hints">
                <h3 class="title is-5">Indices</h3>
                <div v-for="(hint, index) in hints" :key="index" class="hint-item">
                    {{ hint }}
                </div>
            </div>

            <div v-if="hasFoundAnswer" class="found-answer">
                <div class="notification is-success">
                    <h3 class="title is-4">Félicitations !</h3>
                    <p>Vous avez trouvé le bon jeu !</p>
                </div>
            </div>
        </div>
    </div>

    <NextRoundModal 
        :active="showNextRoundModal" 
        @start-next-round="startNextRound"
    />
</template>

<style scoped>
.game-play {
    display: grid;
    grid-template-columns: 2fr 5fr;
    gap: 2rem;
    height: 100%;
    padding: 2rem;
}

.game-header {
    margin-bottom: 2rem;
    padding: 1rem;
    background-color: var(--gamdle-darker-color);
    border-radius: 8px;
}

.left-side {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.round-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    justify-content: center;
}

.tag {
    font-size: 0.9em;
    padding: 0.4rem 0.6rem;
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
    position: relative;
}

.guess-input input {
    width: 100%;
    height: 4rem;
    font-size: 1.5rem;
    text-align: center;
}

.notification {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 1rem;
    z-index: 1;
}

.suggestions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background-color: var(--gamdle-darker-color);
    padding: 0.5rem;
    border-radius: 8px;
}

.suggestion-item {
    padding: 0.5rem 1rem;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.suggestion-item:hover,
.suggestion-item.is-selected {
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