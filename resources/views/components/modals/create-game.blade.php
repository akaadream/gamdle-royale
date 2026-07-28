<div x-data="{ private: false, showPassword: false }" x-show="createVisible" class="modal is-active" x-cloak>
    <div x-on:click="createVisible = false" class="modal-background"></div>
    <div class="modal-card" x-show="createVisible" x-transition>
        <header class="modal-card-head">
            <p class="modal-card-title">Créer une partie</p>
            <button x-on:click="createVisible = false" class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field">
                <label class="label">Nom de la partie</label>
                <div class="control">
                    <input wire:model="name" class="input" type="text" placeholder="Nom">
                </div>
            </div>

            <div class="field">
                <label class="label">
                    <input type="checkbox" x-model="private">
                    Salon privé
                </label>
            </div>

            <div class="field" x-show="private">
                <label class="label">Mot de passe</label>
                <input wire:model="password" class="input" x-bind:type="showPassword ? 'text' : 'password'" placeholder="Mot de passe">
            </div>

            <div class="field" x-show="private">
                <label class="label">
                    <input type="checkbox" x-model="showPassword">
                    Afficher le mot de passe
                </label>
            </div>
        </section>
        <footer class="modal-card-foot">
            <div class="buttons">
                <button wire:click="create" class="button is-success">Créer</button>
            </div>
        </footer>
    </div>
</div>
