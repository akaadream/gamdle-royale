import {Schema, MapSchema, type} from "@colyseus/schema";

export class Player extends Schema {
    @type("string") username: string = "";
    @type("boolean") ready: boolean = false;
    @type("number") points: number = 0;
    @type("boolean") connected: boolean = true;
}

export class GameRoomState extends Schema {
    @type({ map: Player }) players = new MapSchema<Player>();
    @type("number") countdown: number = 5;
    @type("boolean") everyoneReady: boolean = false;
    @type("number") currentRound: number = 0;
}
