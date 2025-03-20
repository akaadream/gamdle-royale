@extends('main')

@section('page-content')
    <script src="https://unpkg.com/colyseus.js@^0.16.0/dist/colyseus.js"></script>
    <script>
        let connectedRoom;
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('game-name');
            if (input) {
                input.addEventListener('keydown', keypressed);
            }

            let client = new Colyseus.Client("ws://localhost:2567");
            client.joinOrCreate('game').then(room => {
                console.log("Joined successfully", room);
                connectedRoom = room;

                room.onMessage("bad_answer", (message) => {
                    console.log("Bad answer");
                });

                room.onMessage("good_answer", (message) => {
                    console.log("You found the game !!");
                })
            }).catch(e => {
                console.error("Join error", e);
            });
        });

        function keypressed(event) {
            const input = document.getElementById('game-name');

            // Press enter
            if (event.keyCode === 13) {
                console.log("ENTER pressed");
                const text = input.value;
                if (connectedRoom != null) {
                    connectedRoom.send("attempt", {
                        "game_name": text
                    });
                }
                event.preventDefault();
            }
        }
    </script>
@endsection
