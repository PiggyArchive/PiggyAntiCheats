<?php

namespace PiggyAntiCheats;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\Listener;
use pocketmine\network\protocol\AdventureSettingsPacket;

class EventListener implements Listener {
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if ($player->hasPermission("piggyanticheat.notify")) {
            $this->plugin->notified[$player->getName()] = true;
        }
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        $this->plugin->blocks[$player->getName()] = + pow($to->x - $from->x, 2) + pow($to->z - $from->z, 2); //Don't get distance for y
        $this->plugin->blocksup[$player->getName()] = + ($to->y - $from->y); //Returns negative if going down :)
        //Fly
        $distance = round($this->plugin->blocksup[$player->getName()], 3) * 1000;
        $maxblock = $this->plugin->getConfig()->getNested("detection.jump");
        if ($player->getEffect(8) !== null) {
            $maxblock = $maxblock + ($player->getEffect(8)->getAmplifier() / 8 + 0.46);
        }
        if ($this->plugin->secondinair[$player->getName()] > 1 && $distance >= 0 && $player->getAllowFlight() !== true && $distance < $maxblocks) {
            $ground = $player->getLevel()->getHighestBlockAt(floor($to->x), floor($to->z));
            if ($to->y - $this->getConfig()->getNested("detection.fly") > $ground) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify") && isset($this->plugin->notified[$player->getName()])) {
                        $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("fly")));
                    }
                }
                $event->setCancelled();
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        unset($this->plugin->blocks[$player->getName()]);
        unset($this->plugin->points[$player->getName()]);
    }

    public function onRecieve(DataPacketReceiveEvent $event) {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof AdventureSettingsPacket) {
            if (($packet->allowFlight || $packet->isFlying) && $player->getAllowFlight() !== true) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify") && isset($this->plugin->notified[$player->getName()])) {
                        $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("fly")));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            if ($packet->noClip && $player->isSpectator() !== true) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify") && isset($this->plugin->notified[$player->getName()])) {
                        $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("no-clip")));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            $player->sendSettings();
            $event->setCancelled();
        }
    }

}
