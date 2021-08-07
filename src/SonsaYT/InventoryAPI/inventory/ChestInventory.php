<?php

namespace SonsaYT\InventoryAPI\inventory;

use pocketmine\block\Block;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\tile\Nameable;
use SonsaYT\InventoryAPI\Main;

class ChestInventory extends ContainerInventory {

    public $main;
    protected $name = "Chest";

    protected $viewOnly = false;
    protected $clickCallback = null;
    protected $closeCallback = null;

    public function __construct(Main $main) {
        parent::__construct(new Vector3(), [], null, null);
        $this->main = $main;
    }

    public function getDefaultSize() : int{
        return 27;
    }

    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $value){
        $this->name = $value;
    }

    public function setViewOnly(bool $value = true){
        $this->viewOnly = $value;
    }

    public function isViewOnly() : bool{
        return $this->viewOnly;
    }

    public function getClickCallback(){
        return $this->clickCallback;
    }

    public function setClickCallback(?callable $callable){
        $this->clickCallback = $callable;
    }

    public function getCloseCallback(){
        return $this->closeCallback;
    }

    public function setCloseCallback(?callable $callable){
        $this->closeCallback = $callable;
    }

    public function onClose(Player $who) : void {
        parent::onClose($who);
        // Real block
        $packet = new UpdateBlockPacket();
        $packet->x = $this->holder->x;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;
        $packet->blockRuntimeId = $who->getLevel()->getBlock($this->holder)->getRuntimeId();
        $packet->flags = UpdateBlockPacket::FLAG_NETWORK;
        $who->dataPacket($packet);
        $closeCallback = $this->getCloseCallback();
        if ($closeCallback !== null){
            $closeCallback($who, $this);
        }
    }

    public function send(Player $player){

        // Set holder
        $this->holder = new Vector3((int)$player->getX(), (int)$player->getY() + 3, (int)$player->getZ());

        // Fake block
        $packet = new UpdateBlockPacket();
        $packet->x = $this->holder->x;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;
        $packet->blockRuntimeId = Block::get(54, 0)->getRuntimeId();
        $packet->flags = UpdateBlockPacket::FLAG_NETWORK;
        $player->sendDataPacket($packet);

        // Fake tile
        $tags = new CompoundTag();
        $tags->setString(Nameable::TAG_CUSTOM_NAME, $this->getName());

        $packet = new BlockActorDataPacket();
        $packet->x = $this->holder->x;
        $packet->y = $this->holder->y;
        $packet->z = $this->holder->z;

        $writer = new NetworkLittleEndianNBTStream();
        $packet->namedtag = $writer->write($tags);
        $player->dataPacket($packet);

        // Add window
        $player->addWindow($this);
    }

}