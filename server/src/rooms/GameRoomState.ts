import { Schema, type, MapSchema } from "@colyseus/schema";

export class Player extends Schema {
    @type("string") username: string = "";
    @type("boolean") isTyping: boolean = false;
    @type("string") lastGuess: string = "";
    @type("boolean") foundAnswer: boolean = false;
}

export class GameRoomState extends Schema {
    @type({ map: Player }) players = new MapSchema<Player>();
    @type("string") currentGame: string = "";
    @type("string") gameState: "waiting" | "countdown" | "playing" | "finished" = "waiting";
    @type("number") roundNumber: number = 0;
    @type("number") maxRounds: number = 10;
    @type("number") roundTimeLimit: number = 300; // 5 minutes en secondes
    @type("number") roundStartTime: number = 0;
    @type("number") countdown: number = 0;
} 