<?php

namespace PiggyAntiCheats;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\Listener;
use pocketmine\network\protocol\AdventureSettingsPacket;

class EventListener implements Listener {
    public function __construct($plugin) {
        $this->plugin = $plugin;
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
    
    public function onRecieve(DataPacketReceiveEvent $event){
        $player = $event->getPlayer();
        $packet = $event->getPacket();    
        if($packet instanceof AdventureSettingsPacket){
            if(($packet->allowFlight || $packet->isFlying) && $player->getAllowFlight() !== true){
                $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("fly")));
                $this->plugin->points[$player->getName()]++;
            }
            if($packet->noClip && $player->isSpectator() !== true){
                $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("no-clip")));
                $this->plugin->points[$player->getName()]++;
            }
            $player->sendSettings();
        }
    }

}
