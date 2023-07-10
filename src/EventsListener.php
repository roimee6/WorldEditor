<?php /** @noinspection PhpUnused */

namespace WorldEditor;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class EventsListener implements Listener
{
    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();

        if ($player->getInventory()->getItemInHand()->equals(VanillaItems::GOLDEN_AXE())) {
            $event->cancel();

            $position = $event->getBlock()->getPosition();

            Main::getInstance()->setPosition(
                $player, "pos1",
                $position->getFloorX(),
                $position->getFloorY(),
                $position->getFloorZ()
            );

            $player->sendMessage("§aFirst pos set as: " . implode(", ", Main::getInstance()->getPosition($player, "pos1")));
        }
    }

    public function onUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getPlayer()->getTargetBlock(4 * 16);

        $this->setSecondPos($player, $block);
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($event->getAction() === $event::RIGHT_CLICK_BLOCK) {
            $this->setSecondPos($player, $block);
        }
    }

    public function setSecondPos(Player $player, ?Block $block): void
    {
        if ($block instanceof Block && $player->getInventory()->getItemInHand()->equals(VanillaItems::GOLDEN_AXE())) {
            $position = $block->getPosition();

            Main::getInstance()->setPosition(
                $player, "pos2",
                $position->getFloorX(),
                $position->getFloorY(),
                $position->getFloorZ()
            );

            $player->sendMessage("§aSecond pos set as: " . implode(", ", Main::getInstance()->getPosition($player, "pos2")));
        }
    }
}