export class Game {
    name: string;
    parent_name?: string;
    hints: string[];

    currentHintIndex = 0;

    constructor(
        name: string,
        game_modes: string,
        platforms: string,
        genres: string,
        themes: string,
        perspectives: string,
        developers: string,
        first_release_date: string,
        screenshot: string) {
        this.name = name;

        this.hints = [];

        this.hints.push(game_modes);
        this.hints.push(platforms);
        this.hints.push(genres);
        this.hints.push(themes);
        this.hints.push(perspectives);
        this.hints.push(developers);
        this.hints.push(first_release_date);
        this.hints.push(screenshot);

        this.currentHintIndex = 0;
        this.parent_name = null;
    }

    nextHint(): string {
        this.currentHintIndex++;

        if (this.currentHintIndex >= this.hints.length - 1) {
            return "";
        }

        return this.hints[this.currentHintIndex];
    }

    goodAnswer(answer: string): boolean {
        return answer === this.name || (this.parent_name && answer === this.parent_name);
    }
}
