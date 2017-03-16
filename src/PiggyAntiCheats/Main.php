<?php

namespace PiggyAntiCheats;

use PiggyAntiCheats\Tasks\AntiCheatsTick;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {
    public $blocks;
    public $blocksup;
    public $lasty;
    public $points;
    public $notified;
    public $lang;

    public function onEnable() {
        $this->saveDefaultConfig();
        if (!file_exists($this->getDataFolder() . "lang_" . $this->getConfig()->getNested("message.lang") . ".yml")) {
            if ($this->getResource("lang_" . $this->getConfig()->getNested("message.lang") . ".yml") !== null) {
                $this->saveResource("lang_" . $this->getConfig()->getNested("message.lang") . ".yml");
                $this->lang = new Config($this->getDataFolder() . "lang_" . $this->getConfig()->getNested("message.lang") . ".yml");
            } else {
                $this->getLogger()->error("Unknown language: " . $this->getConfig()->getNested("message.lang") . ". Using english.");
                if (!file_exists($this->getDataFolder() . "lang_eng.yml")) {
                    $this->saveResource("lang_eng.yml");
                }
                $this->lang = new Config($this->getDataFolder() . "lang_eng.yml");
            }
        } else {
            $this->lang = new Config($this->getDataFolder() . "lang_" . $this->getConfig()->getNested("message.lang") . ".yml");
        }
        $this->getServer()->getCommandMap()->register('pac', new PACCommand('pac', $this));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new AntiCheatsTick($this), 20);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info("§aEnabled.");
    }

    public function getMessage($message) {
        return str_replace("&", "§", $this->lang->getNested($message));
    }

}
