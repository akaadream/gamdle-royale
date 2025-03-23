import {GameRoomState, Player} from "./schema/GameRoomState";
import {Client, Room} from "@colyseus/core";
import {Game} from "../models/Game";
import {Delayed} from "colyseus";

export class GameRoom extends Room<GameRoomState> {
    state = new GameRoomState();
    game: Game;
    countdownInterval: Delayed;
    roundInterval: Delayed;
    roundDuration: number = 60_000;

    async onCreate(options: any) {
        this.onMessage('start_round', (client, data) => {
            if (this.state.everyoneReady) {
                this.randomGame().then(() => {
                    this.countdownInterval = this.clock.setTimeout(() => this.countdown(), 1_000);
                })
            }
        });

        this.onMessage('ready', (client, data) => {
            this.setReady(true, client.sessionId);
            this.checkEveryoneReady();
        });

        this.onMessage('not_ready', (client, data) => {
            this.setReady(false, client.sessionId);
            this.checkEveryoneReady();
        });

        this.onMessage('attempt', (client, data) => {
            if (data.game_name) {
                if (data.game_name === this.game.name || (this.game.parent_name && data.game_name === this.game.parent_name)) {
                    // Good answer
                    client.send('good_answer');
                    this.broadcast('player_found_answer', 'A player found the answer', {
                        except: client
                    });
                }
                else {
                    // Bad answer
                    client.send('bad_answer');
                }
            }
        });
    }



    countdown() {
        this.state.countdown--;
        if (this.state.countdown === 0) {
            this.countdownInterval.clear();

            this.state.currentRound++;
            this.roundInterval = this.clock.setInterval(() => this.hint(), 10_000);
            this.clock.setTimeout(() => this.roundEnding(), this.roundDuration);
        }
    }

    hint() {
        this.broadcast('hint', this.game.nextHint());
    }

    roundEnding() {
        // TODO: round ending
    }

    checkEveryoneReady(): void {
        this.state.players.forEach((player, key) => {
            if (!player.ready) {
                this.state.everyoneReady = false;
            }
        });

        this.state.everyoneReady = true;
    }

    setReady(ready: boolean, sessionId: string) {
        const player = this.state.players.get(sessionId);
        if (player) {
            player.ready = ready;
        }
    }

    onJoin(client: Client, options: any) {
        const player = new Player();
        if (options.username) {
            player.username = options.username;
        }
        else {
            player.username = `Guest ${this.state.players.size + 1}`;
        }

        this.state.players.set(client.sessionId, player);
    }

    async onLeave(client: Client<any, any>, consented?: boolean) {
        this.state.players.get(client.sessionId).connected = false;

        try {
            if (consented) {
                throw new Error("Consented leave");
            }

            await this.allowReconnection(client, 20);
            this.state.players.get(client.sessionId).connected = true;
        } catch (e) {
            this.state.players.delete(client.sessionId);
        }
    }

    /**
     * Find a random game and assign it to the room
     */
    async randomGame() {
        const response = await fetch('http://gamdle-royale.test/api/v1/random-game');
        const data = await response.json();

        this.game = new Game(
            data.name,
            data.game_modes,
            data.platforms,
            data.genres,
            data.themes,
            data.perspectives,
            data.developers,
            data.first_release_date,
            data.screenshot
        );

        if (data.parent_name) {
            this.game.parent_name = data.parent_name;
        }
    }
}
