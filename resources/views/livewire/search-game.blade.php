<div class="flex flex-col w-full text-white p-4">
    <label class="mb-2" for="game-name">Nom du jeu :</label>
    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nom du jeu" name="game-name" id="game-name" list="games" class="input" />
    <datalist id="games">
        @foreach ($games as $game)
            <option value="{{ $game->name }}">
        @endforeach
    </datalist>
</div>
