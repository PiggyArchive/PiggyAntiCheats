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
            //Create if not exist
            if (!isset($this->plugin->blocks[$player->getName()])) {
                $this->plugin->blocks[$player->getName()] = 0;
            }
            if (!isset($this->plugin->blocksup[$player->getName()])) {
                $this->plugin->blocksup[$player->getName()] = 0;
            }
            if (!isset($this->plugin->points[$player->getName()])) {
                $this->plugin->points[$player->getName()] = 0;
            }
            //Speed
            $speed = $this->plugin->blocks[$player->getName()];
            $maxspeed = $this->plugin->getConfig()->getNested("detection.speed");
            ;
            if ($player->getEffect(1) !== null) {
                $maxspeed = $maxspeed + (0.20 * $player->getEffect(1)->getAmplifier());
            }
            if ($speed > $maxspeed) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify")) {
                        $p->sendMessage(str_replace("{speed}", $speed, str_replace("{player}", $player->getName(), $this->plugin->getMessage("too-fast"))));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            //Super Jump
            $blocks = $this->plugin->blocksup[$player->getName()];
            $maxblock = $this->plugin->getConfig()->getNested("detection.jump");
            if ($player->getEffect(8) !== null) {
                $maxblock = $maxblock + ($player->getEffect(1)->getAmplifier() / 8 + 0.46);
            }
            if ($blocks > $maxblock && $player->getAllowFlight() !== true) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify")) {
                        $p->sendMessage(str_replace("{blocks}", $blocks, str_replace("{player}", $player->getName(), $this->plugin->getMessage("high-jump"))));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            //Reset blocks per second
            $this->plugin->blocks[$player->getName()] = 0;
            $this->plugin->blocksup[$player->getName()] = 0;
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
