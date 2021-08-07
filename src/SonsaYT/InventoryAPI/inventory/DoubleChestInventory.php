<?php

namespace SonsaYT\InventoryAPI\inventory;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\tile\Nameable;
use SonsaYT\InventoryAPI\Main;
use SonsaYT\InventoryAPI\task\DelayTask;

class DoubleChestInventory extends ChestInventory {

    public $main;
    protected $name = "Double Chest";

    public function __construct(Main $main) {
        parent::__construct($main);
        $this->main = $main;
    }

    public function getDefaultSize() : int{
        return 54;
    }

    public function onClose(Player $who) : void {
        // Real block
        $packet = new UpdateBlockPacket();
        $packet->x = $this->holder->x + 1;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;
        $packet->blockRuntimeId = $who->getLevel()->getBlock($this->holder->add(1, 0, 0))->getRuntimeId();
        $packet->flags = UpdateBlockPacket::FLAG_NETWORK;
        $who->dataPacket($packet);
        parent::onClose($who);
    }

    public function send(Player $player){

        // Set holder
        $this->holder = new Vector3((int)$player->getX(), (int)$player->getY() + 3, (int)$player->getZ());

        // Fake block left
        $packet = new UpdateBlockPacket();
        $packet->x = $this->holder->x;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;
        $packet->blockRuntimeId = Block::get(54, 0)->getRuntimeId();
        $packet->flags = UpdateBlockPacket::FLAG_NETWORK;
        $player->sendDataPacket($packet);

        // Fake block right
        $packet = new UpdateBlockPacket();
        $packet->x = $this->holder->x + 1;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;
        $packet->blockRuntimeId = Block::get(54, 0)->getRuntimeId();
        $packet->flags = UpdateBlockPacket::FLAG_NETWORK;
        $player->sendDataPacket($packet);

        // Fake tile left
        $tags = new CompoundTag();
        $tags->setString(Nameable::TAG_CUSTOM_NAME, $this->getName());
        $tags->setInt("pairx", $this->holder->x + 1);
        $tags->setInt("pairz", $this->holder->z);

        $packet = new BlockActorDataPacket();
        $packet->x = $this->holder->x;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;

        $writer = new NetworkLittleEndianNBTStream();
        $packet->namedtag = $writer->write($tags);
        $player->dataPacket($packet);

        // Fake tile right
        $tags = new CompoundTag();
        $tags->setInt("pairx", $this->holder->x); // Not needed?
        $tags->setInt("pairz", $this->holder->z); // Not needed?

        $packet = new BlockActorDataPacket();
        $packet->x = $this->holder->x + 1;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;

        $writer = new NetworkLittleEndianNBTStream();
        $packet->namedtag = $writer->write($tags);
        $player->dataPacket($packet);

        // Add window with delay
        $this->main->getScheduler()->scheduleDelayedTask(new DelayTask($player, $this), 20);
    }

}