<?php

use App\Models\Room;
use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public Room $room;
    public User $player;

    public int $round = 0;
    public int $roundTime = 0;

    /**
     * @var array<string> $hints
     */
    public array $hints = [];

    /**
     * @var array<string> $suggestions
     */
    public array $suggestions = [];

    public function mount($roomId): void
    {
        $this->room = Room::where('code', $roomId)->firstOrFail();
        $this->player = auth()->user();
    }
};
