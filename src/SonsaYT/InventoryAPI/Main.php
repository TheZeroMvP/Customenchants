<?php

namespace SonsaYT\InventoryAPI;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use SonsaYT\InventoryAPI\inventory\ChestInventory;
use SonsaYT\InventoryAPI\inventory\DoubleChestInventory;

class Main extends PluginBase implements Listener {

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function createChestGUI(){
        return new ChestInventory($this);
    }

    public function createDoubleChestGUI(){
        return new DoubleChestInventory($this);
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();
        foreach ($transaction->getActions() as $action){
            if ($action instanceof SlotChangeAction){
                $inventory = $action->getInventory();
                if ($inventory instanceof ChestInventory){
                    $event->setCancelled($inventory->isViewOnly());
                    $clickCallback = $inventory->getClickCallback();
                    if ($clickCallback !== null){
                        $clickCallback($player, $inventory, $action->getSourceItem(), $action->getTargetItem(), $action->getSlot());
                    }
                }
            }
        }
    }

}