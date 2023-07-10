<?php

namespace WorldEditor\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class Wand extends Command
{
    public function __construct()
    {
        $this->setPermissions([DefaultPermissions::ROOT_OPERATOR]);
        parent::__construct("wand", "", "");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            $sender->getInventory()->setItemInHand(VanillaItems::GOLDEN_AXE());
            $sender->sendMessage("§aYou have been given the wand!");
        }
    }
}