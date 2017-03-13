<?php

namespace PiggyAntiCheats\Tasks;

use pocketmine\scheduler\PluginTask;

class AntiCheatsTick extends PluginTask {
    public function __construct($plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick) {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if(!isset($this->plugin->blocks[$player->getName()])){
                $this->plugin->blocks[$player->getName()] = 0;
            }
            if(!isset($this->plugin->points[$player->getName()])){
                $this->plugin->points[$player->getName()] = 0;
            }
            $speed = $this->plugin->blocks[$player->getName()];
            if ($speed > 1) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify")) {
                        $p->sendMessage(str_replace("{speed}", $speed, str_replace("{player}", $player->getName(), $this->plugin->getMessage("too-fast"))));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            $this->plugin->blocks[$player->getName()] = 0;
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
