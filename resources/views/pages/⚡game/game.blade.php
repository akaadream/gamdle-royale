<div class="game-play">
    <div class="left-side">
        <div class="round-info">
            <span class="tag is-big is-primary">Round {{ $round + 1 }}/10</span>
            <span class="tag is-big is-info">Temps restant : {{ $roundTime }}</span>
            <span class="tag is-big is-warning">Prochain indice dans : {{ $roundTime }}</span>
        </div>

        <div class="players-list">
            <h2 class="title is-4">Joueurs</h2>
            <div v-for="player in players" :key="player.username" class="player-item">
                <span class="player-name">{{ $player->name }}</span>
                <span v-if="player.isTyping" class="tag is-info">Écrit...</span>
                <span v-if="player.lastGuess" class="tag is-warning">Last guess</span>
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

        <div wire:show="suggestions" class="suggestions">
            @foreach ($suggestions as $suggestion)
                <div
                    class="suggestion-item"
                    :class="{ 'is-selected': index === selectedSuggestionIndex }"
                    @click=""
                >
                    {{ $suggestion }}
                </div>
            @endforeach
        </div>

        <div v-if="!hasFoundAnswer" class="hints">
            <h3 class="title is-5">Indices</h3>
            @foreach ($hints as $hint)
                <div class="hint-item">
                    {{ $hint }}
                </div>
            @endforeach
        </div>

        <div v-if="hasFoundAnswer" class="found-answer">
            <div class="notification is-success">
                <h3 class="title is-4">Félicitations !</h3>
                <p>Vous avez trouvé le bon jeu !</p>
            </div>
        </div>
    </div>
</div>
