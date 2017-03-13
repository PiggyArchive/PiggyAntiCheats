<?php

namespace PiggyAntiCheats;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->plugin->blocks[$player->getName()] = 0;
        $this->plugin->points[$player->getName()] = 0;
    }
    
    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        $this->plugin->blocks[$player->getName()] =+ $from->distance($to);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        unset($this->plugin->blocks[$player->getName()]);
        unset($this->plugin->points[$player->getName()]);
    }

}
