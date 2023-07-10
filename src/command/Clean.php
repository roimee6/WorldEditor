<?php

namespace WorldEditor\command;

use WorldEditor\Main;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\world\World;

class Clean extends Command
{
    private array $configuration = [];
    private array $blocks = [];

    public function __construct()
    {
        $this->setPermissions([DefaultPermissions::ROOT_OPERATOR]);
        parent::__construct("clean", "", "");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            $pos = Main::getInstance()->getPosition($sender, "pos1");
            $pos2 = Main::getInstance()->getPosition($sender, "pos2");

            if ($pos === null || $pos2 === null) {
                $sender->sendMessage("§cPlease set the positions first!");
                return;
            }

            $value = boolval($args[0] ?? false);

            if (!$value) {
                $this->configuration = [];
            }

            $sender->sendMessage("§aCleaning the area...");

            $minX = min($pos[0], $pos2[0]);
            $minY = min($pos[1], $pos2[1]);
            $minZ = min($pos[2], $pos2[2]);

            $maxX = max($pos[0], $pos2[0]);
            $maxY = max($pos[1], $pos2[1]);
            $maxZ = max($pos[2], $pos2[2]);

            $world = $sender->getWorld();

            for ($x = $minX; $x <= $maxX; $x++) {
                for ($y = $minY; $y <= $maxY; $y++) {
                    for ($z = $minZ; $z <= $maxZ; $z++) {
                        $this->blocks[] = [$x, $y, $z];
                    }
                }
            }

            $this->cleaning($sender, $world);
        }
    }

    private function cleaning(Player $sender, World $world): void
    {
        foreach ($this->blocks as $key => $block) {
            list($x, $y, $z) = $block;
            $block = $world->getBlockAt($x, $y, $z);

            var_dump($x, $y, $z);

            if (!isset($this->configuration[$block->getName()])) {
                $menu = InvMenu::create(InvMenuTypeIds::TYPE_HOPPER);

                $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($world, $block): void {
                    $player = $transaction->getPlayer();

                    if ($transaction->getItemClicked()->getCustomName() === "§rRemove the Block") {
                        $this->configuration[$block->getName()] = 0;
                    } else {
                        $this->configuration[$block->getName()] = 1;
                    }

                    $player->removeCurrentWindow();
                    $this->cleaning($player, $world);
                }));

                $menu->getInventory()->setItem(0, $block->asItem());
                $menu->getInventory()->setItem(1, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setcustomName("§rRemove the Block"));
                $menu->getInventory()->setItem(2, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN())->asItem()->setcustomName("§rKeep the block"));

                $menu->send($sender);
                return;
            }

            if ($this->configuration[$block->getName()] === 0) {
                $world->setBlockAt($x, $y, $z, VanillaBlocks::AIR());

                $tile = $world->getTileAt($x, $y, $z);

                if (!is_null($tile)) {
                    $world->removeTile($tile);
                }

                unset($this->blocks[$key]);
            }
        }

        $sender->sendMessage("§aCleaning done!");
    }
}