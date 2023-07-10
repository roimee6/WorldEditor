<?php

namespace WorldEditor\command;

use WorldEditor\Main;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\world\World;

class Flat extends Command
{
    public function __construct()
    {
        $this->setPermissions([DefaultPermissions::ROOT_OPERATOR]);
        parent::__construct("flat", "", "");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            $pos = Main::getInstance()->getPosition($sender, "pos1");
            $pos2 = Main::getInstance()->getPosition($sender, "pos2");

            if (!isset($args[0])) {
                $sender->sendMessage("§cUsage: /flat [Y] [RADIUS=5]");
                return;
            }

            $y = intval($args[0]);
            $radius = intval($args[1] ?? 5);

            $time = time();

            if ($pos === null || $pos2 === null) {
                $sender->sendMessage("§cPlease set the positions first!");
                return;
            }

            $sender->sendMessage("§aFlatting the area...");

            $minX = min($pos[0], $pos2[0]);
            $minZ = min($pos[2], $pos2[2]);

            $maxX = max($pos[0], $pos2[0]);
            $maxZ = max($pos[2], $pos2[2]);

            $world = $sender->getWorld();

            for ($x = $minX; $x <= $maxX; $x++) {
                for ($z = $minZ; $z <= $maxZ; $z++) {
                    $nearest = World::Y_MAX;

                    for ($_y = $y - $radius; $_y <= $y + $radius; $_y++) {
                        $block = $world->getBlockAt($x, $_y, $z, false, false);

                        if (!$block->isSameState(VanillaBlocks::AIR()) && $block->isFullCube()) {
                            $nearest = $this->nearestNumber($nearest, $_y, $y);
                        }
                    }

                    for ($_y = $y; $_y <= $y + $radius; $_y++) {
                        if ($_y > $y) {
                            if ($world->isInWorld($x, $_y, $z)) {
                                $world->setBlockAt($x, $_y, $z, VanillaBlocks::AIR());
                            }
                        }
                    }

                    $world->setBlockAt($x, $y, $z, $world->getBlockAt($x, $nearest, $z, false, false));
                }
            }

            $sender->sendMessage("§aFlatting done in " . time() - $time . "s !");
        }
    }

    private function nearestNumber(int $nbr1, int $nbr2, int $target): int
    {
        $diff1 = abs($nbr1 - $target);
        $diff2 = abs($nbr2 - $target);

        if ($diff1 < $diff2) {
            return $nbr1;
        } else {
            return $nbr2;
        }
    }
}