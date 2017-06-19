<?php

namespace PiggyAntiCheats;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;

/**
 * Class EventListener
 * @package PiggyAntiCheats
 */
class EventListener implements Listener
{
    private $plugin;

    /**
     * EventListener constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        unset($this->plugin->points[$player->getName()]);
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onRecieve(DataPacketReceiveEvent $event)
    {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof AdventureSettingsPacket) {
            if (($packet->allowFlight || $packet->isFlying) && $player->getAllowFlight() !== true) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify")) {
                        $player->sendMessage(str_replace("{player}", $player->getName(), $this->plugin->getMessage("fly")));
                    }
                }
                $this->plugin->points[$player->getName()]++;
            }
            if ($packet->noClip && $player->isSpectator() !== true) {
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    if ($p->hasPermission("piggyanticheat.notify")) {
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
