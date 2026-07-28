<div x-show="joinVisible" class="modal is-active" x-cloak>
    <div x-on:click="joinVisible = false" class="modal-background"></div>
    <div class="modal-card" x-show="joinVisible" x-transition>
        <header class="modal-card-head">
            <p class="modal-card-title">Rejoindre une partie</p>
            <button x-on:click="joinVisible = false" class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field">
                <label class="label">Code de la partie</label>
                <div class="control">
                    <input wire:model="code" class="input" type="text" placeholder="Code">
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <div class="buttons">
                <button class="button is-success">Rejoindre</button>
            </div>
        </footer>
    </div>
</div>
