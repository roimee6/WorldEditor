<?php

namespace WorldEditor\command;

use WorldEditor\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class SecondPos extends Command
{
    public function __construct()
    {
        $this->setPermissions([DefaultPermissions::ROOT_OPERATOR]);
        parent::__construct("pos2", "", "", ["2"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            Main::getInstance()->setPosition(
                $sender, "pos2",
                $sender->getPosition()->getFloorX(),
                $sender->getPosition()->getFloorY(),
                $sender->getPosition()->getFloorZ()
            );

            $sender->sendMessage("§aSecond pos set as: " . implode(", ", Main::getInstance()->getPosition($sender, "pos2")));
        }
    }
}