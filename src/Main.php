<?php /** @noinspection ALL */

namespace WorldEditor;

use WorldEditor\command\Clean;
use WorldEditor\command\Commands;
use WorldEditor\command\FirstPos;
use WorldEditor\command\Flat;
use WorldEditor\command\SecondPos;
use WorldEditor\command\Wand;
use CortexPE\Commando\PacketHooker;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    private $pos = [];

    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        if(!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->getServer()->getCommandMap()->register("WorldEditor", new Clean());
        $this->getServer()->getCommandMap()->register("WorldEditor", new FirstPos());
        $this->getServer()->getCommandMap()->register("WorldEditor", new Flat());
        $this->getServer()->getCommandMap()->register("WorldEditor", new SecondPos());
        $this->getServer()->getCommandMap()->register("WorldEditor", new Wand());

        $this->getServer()->getPluginManager()->registerEvents(new EventsListener(), $this);
    }

    public function setPosition(Player $player, string $position, int $x, int $y, int $z): void
    {
        $this->pos[$player->getName()][$position] = [$x, $y, $z];
    }

    public function getPosition(Player $player, string $position): ?array
    {
        return $this->pos[$player->getName()][$position] ?? null;
    }
}