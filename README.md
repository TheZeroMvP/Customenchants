# InventoryAPI
Simple Inventory API for Pocketmine

## How to Install
1) Install devtools
2) Upload the InventoryAPI folder in your plugins folder
3) Restart

## Types
```php
  $chestInv = $this->inventoryApi->createChestGUI(); // Single chest
  $doubleChestInv = $this->inventoryApi->createDoubleChestGUI(); // Double chest
```

## Example
```php

  public function onEnable(){
    $this->inventoryApi = $this->getServer()->getPluginManager()->getPlugin("InventoryAPI");
  }

  public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
    switch ($command){
      case "test":
        if ($sender instanceof Player){
          $this->openMyChest($sender);
        }
      break;
    }
    return true;
  }

  public function openMyChest(Player $player){
    $inventory = $this->inventoryApi->createChestGUI(); // Single chest
    $inventory->setName("Test Chest UwU"); // Set name
    $inventory->setViewOnly(); // Prevent user from getting the item
    $inventory->addItem(Item::get(5, 0, 1)); // Add item
    $inventory->addItem(Item::get(17, 0, 1)); // Add item
    $inventory->setItem(9, Item::get(5, 0, 1)); // Set item
    $inventory->setClickCallback([$this, "clickFunction"]); // Add click callback
    $inventory->setCloseCallback([$this, "closeFunction"]); // Add close callback
    $inventory->send($player); // Send inventory to user
  }

  public function clickFunction(Player $player, Inventory $inventory, Item $source, Item $target, int $slot){
    // Your logic
  }

  public function closeFunction(Player $player, Inventory $inventory){
    // Your logic
  }

```
