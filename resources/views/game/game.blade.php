@extends('layouts.main')

@section('page-content')
    <div class="flex">
        <ul class="list bg-base-100 min-w-[300px] text-white rounded-box shadow-md">
            <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">Liste des joueurs</li>

            <li class="list-row">
                <div class="grid place-items-center">Akadream</div>
                <span class="badge badge-accent">Jeu trouvé !</span>
            </li>

            <li class="list-row">
                <div>Cambart</div>
            </li>

            <li class="list-row">
                <div>Mike</div>
            </li>
        </ul>

        <div class="divider lg:divider-horizontal"></div>

        <div class="flex flex-col w-full">
            <livewire:search-game></livewire:search-game>

            <div class="flex flex-col text-white">
                <div class="card card-border bg-base-100 hint">
                    <div class="card-body">
                        Thèmes : Fantaisie, Monde ouvert
                    </div>
                </div>

                <div class="card card-border bg-base-100 hint">
                    <div class="card-body">
                        Genre : Plateforme
                    </div>
                </div>

                <div class="card card-border bg-base-100 hint">
                    <div class="card-body">
                        Perspective : Vue latérale
                    </div>
                </div>

                <div class="card card-border bg-base-100 hint">
                    <div class="card-body">
                        Date de sortie : 13 juillet 2012
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/colyseus.js@^0.16.0/dist/colyseus.js"></script>
    <script>
        let client = new Colyseus.Client("ws://localhost:2567");
        client.joinOrCreate('game').then(room => {
            console.log("Joined successfully", room);
        }).catch(e => {
            console.error("Join error", e);
        });
    </script>
@endsection
