<?php

namespace PiggyAntiCheats\Commands;

use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class PACCommand extends VanillaCommand {
    public function __construct($name, $plugin) {
        parent::__construct($name, "Enable anti-cheat notifcations", "/pac");
        $this->setPermission("piggyanticheat.notify");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $currentAlias, array $args) {
        if (!$this->testPermission($sender)) {
            return true;
        }
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cYou must use the command in-game.");
            return false;
        }
        if (isset($this->plugin->notified[$sender->getName()])) {
            unset($this->plugin->notified[$sender->getName()]);
            $sender->sendMessage($this->plugin->getMessage("notifications.disabled"));
        } else {
            $this->plugin->notified[$sender->getName()] = true;
            $sender->sendMessage($this->plugin->getMessage("notifications.enabled"));
        }
    }

}
