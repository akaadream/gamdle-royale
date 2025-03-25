import {Schema, MapSchema, type} from "@colyseus/schema";

export class Player extends Schema {
    @type("string") username: string = "";
    @type("boolean") ready: boolean = false;
    @type("number") points: number = 0;
    @type("boolean") connected: boolean = true;
    @type("boolean") isTyping: boolean = false;
    @type("string") lastGuess: string = "";
    @type("boolean") foundAnswer: boolean = false;
}

export class GameRoomState extends Schema {
    @type({ map: Player }) players = new MapSchema<Player>();
    @type("number") countdown: number = 5;
    @type("boolean") everyoneReady: boolean = false;
    @type("number") roundNumber: number = 0;
    @type("number") maxRounds: number = 10;
    @type("string") gameState: "waiting" | "countdown" | "playing" | "finished" = "waiting";
    @type("number") roundTimeLimit: number = 300; // 5 minutes en secondes
    @type("number") roundStartTime: number = 0;
}
