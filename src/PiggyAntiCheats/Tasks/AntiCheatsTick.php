<?php

namespace PiggyAntiCheats\Tasks;

use PiggyAntiCheats\Main;
use pocketmine\scheduler\PluginTask;

/**
 * Class AntiCheatsTick
 * @package PiggyAntiCheats\Tasks
 */
class AntiCheatsTick extends PluginTask
{
    private $plugin;

    /**
     * AntiCheatsTick constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    /**
     * @param $currentTick
     */
    public function onRun($currentTick)
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            //Punishment
            if ($this->plugin->points[$player->getName()] >= $this->plugin->getConfig()->getNested("punishment.points-til-punishment")) {
                switch ($this->plugin->getConfig()->getNested("punishment.punishment")) {
                    case "kick":
                        $player->kick("No hacking!");
                        break;
                    case "ban":
                        $this->plugin->getServer()->getNameBans()->addBan($player->getName(), "No hacking!");
                        break;
                    case "ipban":
                        $this->plugin->getServer()->getIPBans()->addBan($player->getAddress(), "No hacking!");
                        break;
                    case "none":
                    default:
                        break;
                }
            }
        }
    }

}
