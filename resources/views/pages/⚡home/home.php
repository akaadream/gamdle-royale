<?php

use App\Models\Room;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $code = '';
    public ?string $password = null;

    public function create()
    {
        $room = Room::create([
            'name' => $this->name,
            'code' => Room::generateCode(),
            'owner_id' => auth()->id()
        ]);

        if ($this->password !== null) {
            $room->update(['password' => $this->password]);
        }

        return redirect()->route('game', ['roomId' => $room->code]);
    }

    public function join()
    {

    }
};
