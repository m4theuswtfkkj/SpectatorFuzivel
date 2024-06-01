<?php

namespace Spectator;

use pocketmine\command\{
    ConsoleCommandSender,
    CommandSender,
    Command
};
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\TranslationContainer;

use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    /** @var Config */
    private $config;

    public function onEnable()
    {
        $f = $this->getDataFolder();
        if (!is_dir($f)) {
            @mkdir($f);
        }

        $this->saveDefaultConfig();
        $this->config = new Config($f . "config.yml", Config::YAML);
    }

    public function onCommand(CommandSender $s, Command $c, $label, array $args)
    {
        switch (strtolower($c)) {
            case "spectate":
                if ($s instanceof ConsoleCommandSender)
                {
                    $s->sendMessage("Este comando só pode ser usado em jogo.");
                    return;
                }

                if (!$s->hasPermission($this->getConfig()->get("command.perm")) || !$s->isOp()) {
                    $s->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
                    return;
                }

                if (empty($args[0]))
                {
                    $s->sendMessage($this->getConfig()->get("command.usage"));
                    return;
                }

                $target = $this->getServer()->getPlayer($args[0]);

                if ($target === null)
                {
                    $s->sendMessage($this->getConfig()->get("command.offlineplayer"));
                    return;
                }

                $x = $target->getX();
                $y = $target->getY();
                $z = $target->getZ();
                $world = $target->getLevel();

                $username = $target->getName();
                $ip = $this->getConfig()->get("hide.player.ip") ? $target->getAddress() : "***.***.***.***";
                $cid = $target->getClientId();
                // Não tem a variável de ping por que nem todos os servidores usam uma api que tem essa função :)

                $s->teleport($world->getSafeSpawn());
                $s->teleport(new Vector3($x, $y + 3, $z));
                $s->setGamemode(3);

                $s->sendMessage("§a» §fInformações do Player §a«\n\n§a» §eNick§7: §a{$username}\n§a» §eIP§7: §a{$ip}\n§a» §eClient ID§7: §a{$cid}\n\n§a» /ss para voltar Gm 0");
            break;
            case "ss":
                if (!$s->hasPermission($this->getConfig()->get("command.perm")) || !$s->isOp()) {
                    $s->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
                    return;
                }

                $s->setGamemode(0);
            break;
        }
    }
}