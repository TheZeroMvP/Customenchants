<?php

namespace SonsaYT\InventoryAPI\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use SonsaYT\InventoryAPI\inventory\ChestInventory;

class DelayTask extends Task {

    public $player;
    public $inventory;

    public function __construct(Player $player, ChestInventory $inventory) {
        $this->player = $player;
        $this->inventory = $inventory;
    }

    public function onRun(int $currentTick) {
        $this->player->addWindow($this->inventory);
    }

}