<div x-data="{ createVisible: false, joinVisible: false }" class="hero is-fullheight">
    <div class="hero-body">
        <div class="full-hero">
            <div>
                <p class="title">
                    Gamdle Royale
                </p>

                <p class="subtitle">
                    Affrontez vos amis dans ce jeu où le premier à trouver le jeu a gagné !
                </p>
            </div>

            <div class="buttons mt-6">
                <button x-on:click="createVisible = true" class="button">
                    Créer une partie
                </button>

                <button x-on:click="joinVisible = true" class="button">
                    Rejoindre une partie
                </button>
            </div>
        </div>
    </div>

    @include('components.modals.create-game')
    @include('components.modals.join-game')
</div>
