import {GameRoomState, Player} from "./schema/GameRoomState";
import {Client, Delayed, Room} from "@colyseus/core";
import axios from 'axios';
import https from 'https';

const isDevelopment = process.env.NODE_ENV === 'development';
const API_URL = isDevelopment ? 'https://gamdle-royale.test' : 'https://gamdle-royale.fr';

interface GameData {
    name: string;
    parent_name?: string;
    game_modes: string;
    platforms: string;
    genres: string;
    themes: string;
    perspectives: string;
    developers: string;
    first_release_date: string;
    screenshot: string;

    hints: string[];
    current_hint: number;
}

export class GameRoom extends Room<GameRoomState> {
    private game: GameData | null = null;
    state = new GameRoomState();
    countdownInterval?: Delayed;
    roundInterval?: Delayed;
    roundDuration: number = 60_000;
    countdownDuration: number = 5;

    async onCreate(options: any) {
        this.state = new GameRoomState();
        this.game = options.game;

        this.onMessage('toggle_ready', (client) => {
            const player = this.state.players.get(client.sessionId);
            if (player) {
                player.ready = !player.ready;
            }
        });

        this.onMessage('start', (client) => {
            this.checkEveryoneReady();
        });

        this.onMessage('typing_start', (client) => {
            const player = this.state.players.get(client.sessionId);
            if (player) {
                player.isTyping = true;
                this.broadcast("player_typing", player.username);
            }
        });

        this.onMessage('typing_end', (client) => {
            const player = this.state.players.get(client.sessionId);
            if (player) {
                player.isTyping = false;
                this.broadcast("player_stop_typing", player.username);
            }
        });

        this.onMessage('attempt', (client, message) => {
            const player = this.state.players.get(client.sessionId);
            if (!player) return;

            const gameName = message.game_name.toLowerCase().trim();
            const currentGameName = this.game?.name.toLowerCase() || "";

            if (gameName === currentGameName) {
                player.foundAnswer = true;
                this.broadcast("player_found_answer", player.username);
                this.checkGameEnd();
            } else {
                player.lastGuess = gameName;
                this.broadcast("bad_guess", { username: player.username, guess: gameName });
                this.broadcast("bad_answer", client.sessionId);
            }
        });

        this.onMessage('guess', (client, message) => {
            const player = this.state.players.get(client.sessionId);
            if (!player) return;

            // Mettre à jour le dernier guess du joueur pendant qu'il tape
            player.lastGuess = message.guess;
        });
    }

    countdown() {
        this.state.countdown--;
        if (this.state.countdown === 0) {
            this.countdownInterval?.clear();
            this.state.gameState = 'playing';
            this.randomGame().then(() => {
                this.roundInterval = this.clock.setInterval(() => this.hint(), 10_000);
                this.clock.setTimeout(() => this.roundEnding(), this.roundDuration);
            });
        }
    }

    hint() {
        const nextHint = this.game?.hints[this.game.current_hint];
        if (this.game) {
            this.game.current_hint++;
        }
        
        console.log("hint", nextHint);
        this.broadcast('hint', nextHint);
    }

    roundEnding() {
        // TODO: round ending
    }

    checkEveryoneReady(): void {
        let allReady = true;
        this.state.players.forEach((player) => {
            if (!player.ready) {
                allReady = false;
            }
        });

        this.state.everyoneReady = allReady;

        if (allReady) {
            this.state.gameState = 'countdown';
            this.state.countdown = this.countdownDuration;
            this.countdownInterval = this.clock.setInterval(() => this.countdown(), 1_000);
        }
    }

    setReady(ready: boolean, sessionId: string) {
        const player = this.state.players.get(sessionId);
        if (player) {
            player.ready = ready;
        }
    }

    onJoin(client: Client, options: any) {
        const player = new Player();
        player.username = options.username;
        this.state.players.set(client.sessionId, player);
        // this.broadcast("player_joined", player.username);
    }

    onLeave(client: Client) {
        const player = this.state.players.get(client.sessionId);
        if (player) {
            // this.broadcast("player_left", player.username);
            this.state.players.delete(client.sessionId);
        }
    }

    onDispose() {
        console.log("Room disposed");
    }

    /**
     * Find a random game and assign it to the room
     */
    async randomGame() {
        const response = await axios.get(`${API_URL}/api/v1/random-game`, {
            httpsAgent: new https.Agent({
                rejectUnauthorized: false
            })
        });

        console.log("data", response.data);

        this.game = {
            name: response.data.name,
            parent_name: response.data.parent_name,
            game_modes: response.data.game_modes,
            platforms: response.data.platforms,
            genres: response.data.genres,
            themes: response.data.themes,
            perspectives: response.data.perspectives,
            developers: response.data.developers,
            first_release_date: response.data.first_release_date,
            screenshot: response.data.screenshot,
            hints: [],
            current_hint: 0,
        };

        this.game.hints.push(this.game.game_modes);
        this.game.hints.push(this.game.platforms);
        this.game.hints.push(this.game.genres);
        this.game.hints.push(this.game.themes);
        this.game.hints.push(this.game.perspectives);
        this.game.hints.push(this.game.developers);
        this.game.hints.push(this.game.first_release_date);
        this.game.hints.push(this.game.screenshot);
    }

    private checkGameEnd() {
        const allPlayers = Array.from(this.state.players.values());
        const allFound = allPlayers.every(player => player.foundAnswer);

        if (allFound) {
            this.broadcast("game_end", {
                roundNumber: this.state.roundNumber,
                maxRounds: this.state.maxRounds
            });
        }
    }
}
