<script setup>

</script>

<template>
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
            <div class="flex flex-col w-full text-white p-4">
                <label class="mb-2" for="game-name">Nom du jeu :</label>
                <input type="text" placeholder="Nom du jeu" name="game-name" id="game-name" list="games" class="input" />
                <datalist id="games">
                    @foreach ($games as $game)
                    <option value="{{ $game->name }}">
                        @endforeach
                    </option>
                </datalist>
            </div>

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
</template>
