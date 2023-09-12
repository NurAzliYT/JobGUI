<?php



namespace DraXD\Job;

//Essentials Class
use Closure;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\block\Wood;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\item\{Item, ItemBlock, LegacyStringToItemParser};

use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\scheduler\Task;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\InvMenuEventHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener{
	public $prefix = "[§aJob§2GUI§f] §r";
	public function onEnable(): void{
		//OnEnable 
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->getScheduler()->scheduleRepeatingTask(new Sys($this), 5);
		$this->data = new Config($this->getDataFolder()."job.yml", Config::YAML, array());
		if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
		$this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$this->menus = InvMenu::create(InvMenu::TYPE_CHEST);
		
	}
	
	//Commandsender
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		if($cmd->getName() == "job"){
			if($sender instanceof Player){
				if($this->data->getNested($sender->getName().".has") == true){
					$this->system1($sender);
				}else{
					$this->Introduce($sender);
					return true;
				}
			}else{
				$sender->sendMessage("Please use this in-game");
				return true;
			}
		}
		return true;
	}
	public function getItemFactory(int $id, int $meta = 0, int $count = 1) {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
	public function onMobDeath(EntityDeathEvent $event){
		$entity = $event->getEntity();
		$cause = $entity->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
		    $p = $cause->getDamager();
			if($p instanceof Player){
			    if(!$entity instanceof Player){
			        if($this->data->getNested($p->getName().".job") == "job2"){
			            $this->data->setNested($p->getName().".load", $this->data->getNested($p->getName().".load") + 1);
			            $p->sendTip($this->prefix."\n§b".$this->data->getNested($p->getName().".load")."§f/§a".$this->data->getNested($p->getName().".max"));
                $this->data->save();
			        }
			    }
			}
		}
	}
	
	/**
	 * @priority LOWEST
	 * @ignoreCancelled true
	 * @param BlockBreakEvent $event
	 */
	public function onBlockBreak(BlockBreakEvent $e){
		$p = $e->getPlayer();
		$block = $e->getBlock();
		if($this->data->getNested($p->getName().".job") == "job3"){
		$miner = [10496 => 1, 10080 => 1, 10277 => 1, 10306 => 1, 10079 => 1, 10329 => 1, 10123 => 1, 10445 => 1, 10366 => 1];
        foreach($miner as $id => $point){
            if($e->getBlock()->getTypeId() === $id){
                $this->data->setNested($p->getName().".load", $this->data->getNested($p->getName().".load") + $point);
                $p->sendTip($this->prefix."\n§b".$this->data->getNested($p->getName().".load")."§f/§a".$this->data->getNested($p->getName().".max"));
                $this->data->save();
            }
        }
		}
		if($this->data->getNested($p->getName().".job") == "job1"){
		    if($e->getBlock() instanceof Wood){
		        $this->data->setNested($p->getName().".load", $this->data->getNested($p->getName().".load") + 1);
		            $p->sendTip($this->prefix."\n§b".$this->data->getNested($p->getName().".load")."§f/§a".$this->data->getNested($p->getName().".max"));
                $this->data->save();
		    }
		}
	}
	
	/**
	 * @priority LOWEST
	 * @ignoreCancelled true
	 * @param BlockPlaceEvent $event
	 */
	public function onBlockPlace(BlockPlaceEvent $event){
		$p = $event->getPlayer();
		$block = $event->getBlockAgainst();
		if($this->data->getNested($p->getName().".job") == "job4"){
		    $this->data->setNested($p->getName().".load", $this->data->getNested($p->getName().".load") + 1);
		    $p->sendTip($this->prefix."\n§b".$this->data->getNested($p->getName().".load")."§f/§a".$this->data->getNested($p->getName().".max"));
            $this->data->save();
		}
	}
	//System
	public function system1(Player $s){
	    if($this->data->getNested($s->getName().".job") == "job1"){
	        $this->view1($s);
	    }elseif($this->data->getNested($s->getName().".job") == "job2"){
	        $this->view2($s);
	    }elseif($this->data->getNested($s->getName().".job") == "job3"){
	        $this->view3($s);
	    }elseif($this->data->getNested($s->getName().".job") == "job4"){
	        $this->view4($s);
	    }
	}
	public function system2(Player $s){
	    if($this->data->getNested($s->getName().".level") == 2){
	        $this->data->setNested($s->getName().".id1", 35);
	        $this->data->setNested($s->getName().".m1", 5);
	        $this->data->setNested($s->getName().".n1", "§aLevel 2");
	    }elseif($this->data->getNested($s->getName().".level") < 2){
	        $this->data->setNested($s->getName().".id1", 35);
	        $this->data->setNested($s->getName().".m1", 14);
	        $this->data->setNested($s->getName().".n1", "§cLevel 2");
	    }
	    if($this->data->getNested($s->getName().".level") == 3){
	        $this->data->setNested($s->getName().".id2", 35);
	        $this->data->setNested($s->getName().".m2", 5);
	        $this->data->setNested($s->getName().".n2", "§aLevel 3");
	    }elseif($this->data->getNested($s->getName().".level") < 3){
	        $this->data->setNested($s->getName().".id2", 35);
	        $this->data->setNested($s->getName().".m2", 14);
	        $this->data->setNested($s->getName().".n2", "§cLevel 3");
	    }
	    if($this->data->getNested($s->getName().".level") == 4){
	        $this->data->setNested($s->getName().".id3", 35);
	        $this->data->setNested($s->getName().".m3", 5);
	        $this->data->setNested($s->getName().".n3", "§aLevel 4");
	    }elseif($this->data->getNested($s->getName().".level") < 4){
	        $this->data->setNested($s->getName().".id3", 35);
	        $this->data->setNested($s->getName().".m3", 14);
	        $this->data->setNested($s->getName().".n3", "§cLevel 4");
	    }
	    if($this->data->getNested($s->getName().".level") == 5){
	        $this->data->setNested($s->getName().".id4", 35);
	        $this->data->setNested($s->getName().".m4", 5);
	        $this->data->setNested($s->getName().".n4", "§aLevel 5");
	    }elseif($this->data->getNested($s->getName().".level") < 5){
	        $this->data->setNested($s->getName().".id4", 35);
	        $this->data->setNested($s->getName().".m4", 14);
	        $this->data->setNested($s->getName().".n4", "§cLevel 5");
	    }
	    if($this->data->getNested($s->getName().".level") == 6){
	        $this->data->setNested($s->getName().".id5", 35);
	        $this->data->setNested($s->getName().".m5", 5);
	        $this->data->setNested($s->getName().".n5", "§aLevel 6");
	    }elseif($this->data->getNested($s->getName().".level") < 6){
	        $this->data->setNested($s->getName().".id5", 35);
	        $this->data->setNested($s->getName().".m5", 14);
	        $this->data->setNested($s->getName().".n5", "§cLevel 6");
	    }
	    if($this->data->getNested($s->getName().".level") == 7){
	        $this->data->setNested($s->getName().".id6", 35);
	        $this->data->setNested($s->getName().".m6", 5);
	        $this->data->setNested($s->getName().".n6", "§aLevel 7");
	    }elseif($this->data->getNested($s->getName().".level") < 7){
	        $this->data->setNested($s->getName().".id6", 35);
	        $this->data->setNested($s->getName().".m6", 14);
	        $this->data->setNested($s->getName().".n6", "§cLevel 7");
	    }
	    if($this->data->getNested($s->getName().".level") == 8){
	        $this->data->setNested($s->getName().".id7", 35);
	        $this->data->setNested($s->getName().".m7", 5);
	        $this->data->setNested($s->getName().".n7", "§aLevel 8");
	    }elseif($this->data->getNested($s->getName().".level") < 8){
	        $this->data->setNested($s->getName().".id7", 35);
	        $this->data->setNested($s->getName().".m7", 14);
	        $this->data->setNested($s->getName().".n7", "§cLevel 8");
	    }
	    if($this->data->getNested($s->getName().".level") == 9){
	        $this->data->setNested($s->getName().".id8", 35);
	        $this->data->setNested($s->getName().".m8", 5);
	        $this->data->setNested($s->getName().".n8", "§aLevel 9");
	    }elseif($this->data->getNested($s->getName().".level") < 9){
	        $this->data->setNested($s->getName().".id8", 35);
	        $this->data->setNested($s->getName().".m8", 14);
	        $this->data->setNested($s->getName().".n8", "§cLevel 9");
	    }
	    if($this->data->getNested($s->getName().".level") == 10){
	        $this->data->setNested($s->getName().".id9", 35);
	        $this->data->setNested($s->getName().".m9", 5);
	        $this->data->setNested($s->getName().".n9", "§aLevel 10");
	    }elseif($this->data->getNested($s->getName().".level") < 10){
	        $this->data->setNested($s->getName().".id9", 35);
	        $this->data->setNested($s->getName().".m9", 14);
	        $this->data->setNested($s->getName().".n9", "§cLevel 10");
	    }
	    if($this->data->getNested($s->getName().".level") == 11){
	        $this->data->setNested($s->getName().".id10", 35);
	        $this->data->setNested($s->getName().".m10", 5);
	        $this->data->setNested($s->getName().".n10", "§aLevel 11");
	    }elseif($this->data->getNested($s->getName().".level") < 11){
	        $this->data->setNested($s->getName().".id10", 35);
	        $this->data->setNested($s->getName().".m10", 14);
	        $this->data->setNested($s->getName().".n10", "§cLevel 11");
	    }
	    if($this->data->getNested($s->getName().".level") == 12){
	        $this->data->setNested($s->getName().".id11", 35);
	        $this->data->setNested($s->getName().".m11", 5);
	        $this->data->setNested($s->getName().".n11", "§aLevel 12");
	    }elseif($this->data->getNested($s->getName().".level") < 12){
	        $this->data->setNested($s->getName().".id11", 35);
	        $this->data->setNested($s->getName().".m11", 14);
	        $this->data->setNested($s->getName().".n11", "§cLevel 12");
	    }
	    if($this->data->getNested($s->getName().".level") == 13){
	        $this->data->setNested($s->getName().".id12", 35);
	        $this->data->setNested($s->getName().".m12", 5);
	        $this->data->setNested($s->getName().".n12", "§aLevel 13");
	    }elseif($this->data->getNested($s->getName().".level") < 13){
	        $this->data->setNested($s->getName().".id12", 35);
	        $this->data->setNested($s->getName().".m12", 14);
	        $this->data->setNested($s->getName().".n12", "§cLevel 13");
	    }
	    if($this->data->getNested($s->getName().".level") == 14){
	        $this->data->setNested($s->getName().".id13", 35);
	        $this->data->setNested($s->getName().".m13", 5);
	        $this->data->setNested($s->getName().".n13", "§aLevel 14");
	    }elseif($this->data->getNested($s->getName().".level") < 14){
	        $this->data->setNested($s->getName().".id13", 35);
	        $this->data->setNested($s->getName().".m13", 14);
	        $this->data->setNested($s->getName().".n13", "§cLevel 14");
	    }
	    if($this->data->getNested($s->getName().".level") == 15){
	        $this->data->setNested($s->getName().".id14", 35);
	        $this->data->setNested($s->getName().".m14", 5);
	        $this->data->setNested($s->getName().".n14", "§aLevel 15");
	    }elseif($this->data->getNested($s->getName().".level") < 15){
	        $this->data->setNested($s->getName().".id14", 35);
	        $this->data->setNested($s->getName().".m14", 14);
	        $this->data->setNested($s->getName().".n14", "§cLevel 15");
	    }
	    if($this->data->getNested($s->getName().".level") == 16){
	        $this->data->setNested($s->getName().".id15", 35);
	        $this->data->setNested($s->getName().".m15", 5);
	        $this->data->setNested($s->getName().".n15", "§aLevel 16");
	    }elseif($this->data->getNested($s->getName().".level") < 16){
	        $this->data->setNested($s->getName().".id15", 35);
	        $this->data->setNested($s->getName().".m15", 14);
	        $this->data->setNested($s->getName().".n15", "§cLevel 16");
	    }
	    if($this->data->getNested($s->getName().".level") == 17){
	        $this->data->setNested($s->getName().".id16", 35);
	        $this->data->setNested($s->getName().".m16", 5);
	        $this->data->setNested($s->getName().".n16", "§aLevel 17");
	    }elseif($this->data->getNested($s->getName().".level") < 17){
	        $this->data->setNested($s->getName().".id16", 35);
	        $this->data->setNested($s->getName().".m16", 14);
	        $this->data->setNested($s->getName().".n16", "§cLevel 17");
	    }
	    if($this->data->getNested($s->getName().".level") == 18){
	        $this->data->setNested($s->getName().".id17", 35);
	        $this->data->setNested($s->getName().".m17", 5);
	        $this->data->setNested($s->getName().".n17", "§aLevel 18");
	    }elseif($this->data->getNested($s->getName().".level") < 18){
	        $this->data->setNested($s->getName().".id17", 35);
	        $this->data->setNested($s->getName().".m17", 14);
	        $this->data->setNested($s->getName().".n17", "§cLevel 18");
	    }
	    if($this->data->getNested($s->getName().".level") == 19){
	        $this->data->setNested($s->getName().".id18", 35);
	        $this->data->setNested($s->getName().".m18", 5);
	        $this->data->setNested($s->getName().".n18", "§aLevel 19");
	    }elseif($this->data->getNested($s->getName().".level") < 19){
	        $this->data->setNested($s->getName().".id18", 35);
	        $this->data->setNested($s->getName().".m18", 14);
	        $this->data->setNested($s->getName().".n18", "§cLevel 19");
	    }
	    if($this->data->getNested($s->getName().".level") == 20){
	        $this->data->setNested($s->getName().".id19", 35);
	        $this->data->setNested($s->getName().".m19", 5);
	        $this->data->setNested($s->getName().".n19", "§aLevel 20");
	    }elseif($this->data->getNested($s->getName().".level") < 20){
	        $this->data->setNested($s->getName().".id19", 35);
	        $this->data->setNested($s->getName().".m19", 14);
	        $this->data->setNested($s->getName().".n19", "§cLevel 20");
	    }
	    if($this->data->getNested($s->getName().".level") == 21){
	        $this->data->setNested($s->getName().".id20", 35);
	        $this->data->setNested($s->getName().".m20", 5);
	        $this->data->setNested($s->getName().".n20", "§aLevel 21");
	    }elseif($this->data->getNested($s->getName().".level") < 21){
	        $this->data->setNested($s->getName().".id20", 35);
	        $this->data->setNested($s->getName().".m20", 14);
	        $this->data->setNested($s->getName().".n20", "§cLevel 21");
	    }
	    if($this->data->getNested($s->getName().".level") == 22){
	        $this->data->setNested($s->getName().".id21", 35);
	        $this->data->setNested($s->getName().".m21", 5);
	        $this->data->setNested($s->getName().".n21", "§aLevel 22");
	    }elseif($this->data->getNested($s->getName().".level") < 22){
	        $this->data->setNested($s->getName().".id21", 35);
	        $this->data->setNested($s->getName().".m21", 14);
	        $this->data->setNested($s->getName().".n21", "§cLevel 22");
	    }
	    if($this->data->getNested($s->getName().".level") == 23){
	        $this->data->setNested($s->getName().".id22", 35);
	        $this->data->setNested($s->getName().".m22", 5);
	        $this->data->setNested($s->getName().".n22", "§aLevel 23");
	    }elseif($this->data->getNested($s->getName().".level") < 23){
	        $this->data->setNested($s->getName().".id22", 35);
	        $this->data->setNested($s->getName().".m22", 14);
	        $this->data->setNested($s->getName().".n22", "§cLevel 23");
	    }
	    if($this->data->getNested($s->getName().".level") == 24){
	        $this->data->setNested($s->getName().".id23", 35);
	        $this->data->setNested($s->getName().".m23", 5);
	        $this->data->setNested($s->getName().".n23", "§aLevel 24");
	    }elseif($this->data->getNested($s->getName().".level") < 24){
	        $this->data->setNested($s->getName().".id23", 35);
	        $this->data->setNested($s->getName().".m23", 14);
	        $this->data->setNested($s->getName().".n23", "§cLevel 24");
	    }
	    if($this->data->getNested($s->getName().".level") == 25){
	        $this->data->setNested($s->getName().".id24", 35);
	        $this->data->setNested($s->getName().".m24", 5);
	        $this->data->setNested($s->getName().".n24", "§aLevel 25");
	    }elseif($this->data->getNested($s->getName().".level") < 25){
	        $this->data->setNested($s->getName().".id24", 35);
	        $this->data->setNested($s->getName().".m24", 14);
	        $this->data->setNested($s->getName().".n24", "§cLevel 25");
	    }
	    if($this->data->getNested($s->getName().".level") == 26){
	        $this->data->setNested($s->getName().".id25", 35);
	        $this->data->setNested($s->getName().".m25", 5);
	        $this->data->setNested($s->getName().".n25", "§aLevel 26");
	    }elseif($this->data->getNested($s->getName().".level") < 26){
	        $this->data->setNested($s->getName().".id25", 35);
	        $this->data->setNested($s->getName().".m25", 14);
	        $this->data->setNested($s->getName().".n25", "§cLevel 26");
	    }
	    if($this->data->getNested($s->getName().".level") == 27){
	        $this->data->setNested($s->getName().".id26", 35);
	        $this->data->setNested($s->getName().".m26", 5);
	        $this->data->setNested($s->getName().".n26", "§aLevel 27");
	    }elseif($this->data->getNested($s->getName().".level") < 27){
	        $this->data->setNested($s->getName().".id26", 35);
	        $this->data->setNested($s->getName().".m26", 14);
	        $this->data->setNested($s->getName().".n26", "§cLevel 27");
	    }
	    if($this->data->getNested($s->getName().".level") == 28){
	        $this->data->setNested($s->getName().".id27", 35);
	        $this->data->setNested($s->getName().".m27", 5);
	        $this->data->setNested($s->getName().".n27", "§aLevel 28");
	    }elseif($this->data->getNested($s->getName().".level") < 28){
	        $this->data->setNested($s->getName().".id27", 35);
	        $this->data->setNested($s->getName().".m27", 14);
	        $this->data->setNested($s->getName().".n27", "§cLevel 28");
	    }
	}
	public function system3(Player $s){
	    $max = $this->data->getNested($s->getName().".max");
	    $uang = $this->data->getNested($s->getName().".uang");
	    if($this->data->getNested($s->getName().".load") == $max || $this->data->getNested($s->getName().".load") > $max){
	        $this->data->setNested($s->getName().".level", $this->data->getNested($s->getName().".level") + 1);
	        $this->data->setNested($s->getName().".load", 0);
	        $this->data->setNested($s->getName().".max", $max + 50);
	        $this->eco->addMoney($s, "".$uang);
	        $this->data->save();
	        $s->sendMessage($this->prefix."§aKamu mendapatkan §e".$uang."§6$ §bdari pekerjaanmu");
	    }
	}
	public function system4(Player $s){
	    if($this->data->getNested($s->getName().".level") == 28 || $this->data->getNested($s->getName().".level") > 28){
	        $this->data->setNested($s->getName().".level", 1);
	        $this->data->save();
	        $this->eco->addMoney($s, $this->data->setNested("Max-level-uang", 10000));
	        $name = $s->getName();
	        $s->sendMessage($this->prefix."§aLevel Sudah Max Dan Otomatis Direset Dan Kamu Mendapatkan Uang Sebesar §e".$this->data->getNested("Max-level-uang"));
	    }
	}
	//View1
	public function view1(Player $s){
	    $this->menu->getInventory()->clearAll();
	    $this->menu->setListener(InvMenu::readonly());
		$this->menu->setListener(Closure::fromCallable([$this, "v1"]));
        $this->menu->setName("§0(Job | ".$this->data->getNested($s->getName().".jobname").")");
        //id
        $id1 = $this->data->getNested($s->getName().".id1");
        $id2 = $this->data->getNested($s->getName().".id2");
        $id3 = $this->data->getNested($s->getName().".id3");
        $id4 = $this->data->getNested($s->getName().".id4");
        $id5 = $this->data->getNested($s->getName().".id5");
        $id6 = $this->data->getNested($s->getName().".id6");
        $id7 = $this->data->getNested($s->getName().".id7");
        $id8 = $this->data->getNested($s->getName().".id8");
        $id9 = $this->data->getNested($s->getName().".id9");
        $id10 = $this->data->getNested($s->getName().".id10");
        $id11 = $this->data->getNested($s->getName().".id11");
        $id12 = $this->data->getNested($s->getName().".id12");
        $id13 = $this->data->getNested($s->getName().".id13");
        $id14 = $this->data->getNested($s->getName().".id14");
        $id15 = $this->data->getNested($s->getName().".id15");
        $id16 = $this->data->getNested($s->getName().".id16");
        $id17 = $this->data->getNested($s->getName().".id17");
        $id18 = $this->data->getNested($s->getName().".id18");
        $id19 = $this->data->getNested($s->getName().".id19");
        $id20 = $this->data->getNested($s->getName().".id20");
        $id21 = $this->data->getNested($s->getName().".id21");
        $id22 = $this->data->getNested($s->getName().".id22");
        $id23 = $this->data->getNested($s->getName().".id23");
        $id24 = $this->data->getNested($s->getName().".id24");
        $id25 = $this->data->getNested($s->getName().".id25");
        $id26 = $this->data->getNested($s->getName().".id26");
        $id27 = $this->data->getNested($s->getName().".id27");
        //Meta
        $m1 = $this->data->getNested($s->getName().".m1");
        $m2 = $this->data->getNested($s->getName().".m2");
        $m3 = $this->data->getNested($s->getName().".m3");
        $m4 = $this->data->getNested($s->getName().".m4");
        $m5 = $this->data->getNested($s->getName().".m5");
        $m6 = $this->data->getNested($s->getName().".m6");
        $m7 = $this->data->getNested($s->getName().".m7");
        $m8 = $this->data->getNested($s->getName().".m8");
        $m9 = $this->data->getNested($s->getName().".m9");
        $m10 = $this->data->getNested($s->getName().".m10");
        $m11 = $this->data->getNested($s->getName().".m11");
        $m12 = $this->data->getNested($s->getName().".m12");
        $m13 = $this->data->getNested($s->getName().".m13");
        $m14 = $this->data->getNested($s->getName().".m14");
        $m15 = $this->data->getNested($s->getName().".m15");
        $m16 = $this->data->getNested($s->getName().".m16");
        $m17 = $this->data->getNested($s->getName().".m17");
        $m18 = $this->data->getNested($s->getName().".m18");
        $m19 = $this->data->getNested($s->getName().".m19");
        $m20 = $this->data->getNested($s->getName().".m20");
        $m21 = $this->data->getNested($s->getName().".m21");
        $m22 = $this->data->getNested($s->getName().".m22");
        $m23 = $this->data->getNested($s->getName().".m23");
        $m24 = $this->data->getNested($s->getName().".m24");
        $m25 = $this->data->getNested($s->getName().".m25");
        $m26 = $this->data->getNested($s->getName().".m26");
        $m27 = $this->data->getNested($s->getName().".m27");
        //Name
        $n1 = $this->data->getNested($s->getName().".n1");
        $n2 = $this->data->getNested($s->getName().".n2");
        $n3 = $this->data->getNested($s->getName().".n3");
        $n4 = $this->data->getNested($s->getName().".n4");
        $n5 = $this->data->getNested($s->getName().".n5");
        $n6 = $this->data->getNested($s->getName().".n6");
        $n7 = $this->data->getNested($s->getName().".n7");
        $n8 = $this->data->getNested($s->getName().".n8");
        $n9 = $this->data->getNested($s->getName().".n9");
        $n10 = $this->data->getNested($s->getName().".n10");
        $n11 = $this->data->getNested($s->getName().".n11");
        $n12 = $this->data->getNested($s->getName().".n12");
        $n13 = $this->data->getNested($s->getName().".n13");
        $n14 = $this->data->getNested($s->getName().".n14");
        $n15 = $this->data->getNested($s->getName().".n15");
        $n16 = $this->data->getNested($s->getName().".n16");
        $n17 = $this->data->getNested($s->getName().".n17");
        $n18 = $this->data->getNested($s->getName().".n18");
        $n19 = $this->data->getNested($s->getName().".n19");
        $n20 = $this->data->getNested($s->getName().".n20");
        $n21 = $this->data->getNested($s->getName().".n21");
        $n22 = $this->data->getNested($s->getName().".n22");
        $n23 = $this->data->getNested($s->getName().".n23");
        $n24 = $this->data->getNested($s->getName().".n24");
        $n25 = $this->data->getNested($s->getName().".n25");
        $n26 = $this->data->getNested($s->getName().".n26");
        $n27 = $this->data->getNested($s->getName().".n27");
	    $inventory = $this->menu->getInventory();
	    
	    $inventory->setItem(0, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(1, $this->getItemFactory(258, 0, 1)->setCustomName(" §eWoodCutter "));
	    $inventory->setItem(2, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(3, $this->getItemFactory($id12, $m12, 1)->setCustomName($n12));
	    $inventory->setItem(4, $this->getItemFactory($id13, $m13, 1)->setCustomName($n13));
	    $inventory->setItem(5, $this->getItemFactory($id14, $m14, 1)->setCustomName($n14));
	    $inventory->setItem(6, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(7, $this->getItemFactory($id26, $m26, 1)->setCustomName($n26));
	    $inventory->setItem(8, $this->getItemFactory($id27, $m27, 1)->setCustomName($n27));
	    $inventory->setItem(9, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(10, $this->getItemFactory($id1, $m1, 1)->setCustomName($n1));
	    $inventory->setItem(11, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(12, $this->getItemFactory($id11, $m11, 1)->setCustomName($n11));
	    $inventory->setItem(13, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(14, $this->getItemFactory($id15, $m15, 1)->setCustomName($n15));
	    $inventory->setItem(15, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(16, $this->getItemFactory($id25, $m25, 1)->setCustomName($n25));
	    $inventory->setItem(17, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(18, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(19, $this->getItemFactory($id2, $m2, 1)->setCustomName($n2));
	    $inventory->setItem(20, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(22, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(21, $this->getItemFactory($id10, $m10, 1)->setCustomName($n10));
	    $inventory->setItem(23, $this->getItemFactory($id16, $m16, 1)->setCustomName($n16));
	    $inventory->setItem(24, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(25, $this->getItemFactory($id24, $m24, 1)->setCustomName($n24));
	    $inventory->setItem(26, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(27, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(28, $this->getItemFactory($id3, $m3, 1)->setCustomName($n3));
	    $inventory->setItem(29, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(30, $this->getItemFactory($id9, $m9, 1)->setCustomName($n9));
	    $inventory->setItem(31, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(32, $this->getItemFactory($id17, $m17, 1)->setCustomName($n17));
	    $inventory->setItem(33, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(34, $this->getItemFactory($id23, $m23, 1)->setCustomName($n23));
	    $inventory->setItem(35, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(36, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(37, $this->getItemFactory($id4, $m4, 1)->setCustomName($n4));
	    $inventory->setItem(38, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(39, $this->getItemFactory($id8, $m8, 1)->setCustomName($n8));
	    $inventory->setItem(40, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(41, $this->getItemFactory($id18, $m18, 1)->setCustomName($n18));
	    $inventory->setItem(42, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(43, $this->getItemFactory($id22, $m22, 1)->setCustomName($n22));
	    $inventory->setItem(44, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(45, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(46, $this->getItemFactory($id5, $m5, 1)->setCustomName($n5));
	    $inventory->setItem(47, $this->getItemFactory($id6, $m6, 1)->setCustomName($n6));
	    $inventory->setItem(48, $this->getItemFactory($id7, $m7, 1)->setCustomName($n7));
	    $inventory->setItem(49, $this->getItemFactory(368, 0, 1)->setCustomName(" §cQuit Job "));
	    $inventory->setItem(50, $this->getItemFactory($id19, $m19, 1)->setCustomName($n19));
	    $inventory->setItem(51, $this->getItemFactory($id20, $m20, 1)->setCustomName($n20));
	    $inventory->setItem(52, $this->getItemFactory($id21, $m21, 1)->setCustomName($n21));
	    $inventory->setItem(53, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $this->menu->send($s);
	}
	//view2
	public function view2(Player $s){
	    $this->menu->getInventory()->clearAll();
		$this->menu->setListener(InvMenu::readonly());
		$this->menu->setListener(Closure::fromCallable([$this, "v2"]));
        $this->menu->setName("§0(Job | ".$this->data->getNested($s->getName().".jobname").")");
        //id
        $id1 = $this->data->getNested($s->getName().".id1");
        $id2 = $this->data->getNested($s->getName().".id2");
        $id3 = $this->data->getNested($s->getName().".id3");
        $id4 = $this->data->getNested($s->getName().".id4");
        $id5 = $this->data->getNested($s->getName().".id5");
        $id6 = $this->data->getNested($s->getName().".id6");
        $id7 = $this->data->getNested($s->getName().".id7");
        $id8 = $this->data->getNested($s->getName().".id8");
        $id9 = $this->data->getNested($s->getName().".id9");
        $id10 = $this->data->getNested($s->getName().".id10");
        $id11 = $this->data->getNested($s->getName().".id11");
        $id12 = $this->data->getNested($s->getName().".id12");
        $id13 = $this->data->getNested($s->getName().".id13");
        $id14 = $this->data->getNested($s->getName().".id14");
        $id15 = $this->data->getNested($s->getName().".id15");
        $id16 = $this->data->getNested($s->getName().".id16");
        $id17 = $this->data->getNested($s->getName().".id17");
        $id18 = $this->data->getNested($s->getName().".id18");
        $id19 = $this->data->getNested($s->getName().".id19");
        $id20 = $this->data->getNested($s->getName().".id20");
        $id21 = $this->data->getNested($s->getName().".id21");
        $id22 = $this->data->getNested($s->getName().".id22");
        $id23 = $this->data->getNested($s->getName().".id23");
        $id24 = $this->data->getNested($s->getName().".id24");
        $id25 = $this->data->getNested($s->getName().".id25");
        $id26 = $this->data->getNested($s->getName().".id26");
        $id27 = $this->data->getNested($s->getName().".id27");
        //Meta
        $m1 = $this->data->getNested($s->getName().".m1");
        $m2 = $this->data->getNested($s->getName().".m2");
        $m3 = $this->data->getNested($s->getName().".m3");
        $m4 = $this->data->getNested($s->getName().".m4");
        $m5 = $this->data->getNested($s->getName().".m5");
        $m6 = $this->data->getNested($s->getName().".m6");
        $m7 = $this->data->getNested($s->getName().".m7");
        $m8 = $this->data->getNested($s->getName().".m8");
        $m9 = $this->data->getNested($s->getName().".m9");
        $m10 = $this->data->getNested($s->getName().".m10");
        $m11 = $this->data->getNested($s->getName().".m11");
        $m12 = $this->data->getNested($s->getName().".m12");
        $m13 = $this->data->getNested($s->getName().".m13");
        $m14 = $this->data->getNested($s->getName().".m14");
        $m15 = $this->data->getNested($s->getName().".m15");
        $m16 = $this->data->getNested($s->getName().".m16");
        $m17 = $this->data->getNested($s->getName().".m17");
        $m18 = $this->data->getNested($s->getName().".m18");
        $m19 = $this->data->getNested($s->getName().".m19");
        $m20 = $this->data->getNested($s->getName().".m20");
        $m21 = $this->data->getNested($s->getName().".m21");
        $m22 = $this->data->getNested($s->getName().".m22");
        $m23 = $this->data->getNested($s->getName().".m23");
        $m24 = $this->data->getNested($s->getName().".m24");
        $m25 = $this->data->getNested($s->getName().".m25");
        $m26 = $this->data->getNested($s->getName().".m26");
        $m27 = $this->data->getNested($s->getName().".m27");
        //Name
        $n1 = $this->data->getNested($s->getName().".n1");
        $n2 = $this->data->getNested($s->getName().".n2");
        $n3 = $this->data->getNested($s->getName().".n3");
        $n4 = $this->data->getNested($s->getName().".n4");
        $n5 = $this->data->getNested($s->getName().".n5");
        $n6 = $this->data->getNested($s->getName().".n6");
        $n7 = $this->data->getNested($s->getName().".n7");
        $n8 = $this->data->getNested($s->getName().".n8");
        $n9 = $this->data->getNested($s->getName().".n9");
        $n10 = $this->data->getNested($s->getName().".n10");
        $n11 = $this->data->getNested($s->getName().".n11");
        $n12 = $this->data->getNested($s->getName().".n12");
        $n13 = $this->data->getNested($s->getName().".n13");
        $n14 = $this->data->getNested($s->getName().".n14");
        $n15 = $this->data->getNested($s->getName().".n15");
        $n16 = $this->data->getNested($s->getName().".n16");
        $n17 = $this->data->getNested($s->getName().".n17");
        $n18 = $this->data->getNested($s->getName().".n18");
        $n19 = $this->data->getNested($s->getName().".n19");
        $n20 = $this->data->getNested($s->getName().".n20");
        $n21 = $this->data->getNested($s->getName().".n21");
        $n22 = $this->data->getNested($s->getName().".n22");
        $n23 = $this->data->getNested($s->getName().".n23");
        $n24 = $this->data->getNested($s->getName().".n24");
        $n25 = $this->data->getNested($s->getName().".n25");
        $n26 = $this->data->getNested($s->getName().".n26");
        $n27 = $this->data->getNested($s->getName().".n27");
	    $inventory = $this->menu->getInventory();
	    
	    $inventory->setItem(0, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(1, $this->getItemFactory(267, 0, 1)->setCustomName(" §eButcher "));
	    $inventory->setItem(2, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(3, $this->getItemFactory($id12, $m12, 1)->setCustomName($n12));
	    $inventory->setItem(4, $this->getItemFactory($id13, $m13, 1)->setCustomName($n13));
	    $inventory->setItem(5, $this->getItemFactory($id14, $m14, 1)->setCustomName($n14));
	    $inventory->setItem(6, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(7, $this->getItemFactory($id26, $m26, 1)->setCustomName($n26));
	    $inventory->setItem(8, $this->getItemFactory($id27, $m27, 1)->setCustomName($n27));
	    $inventory->setItem(9, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(10, $this->getItemFactory($id1, $m1, 1)->setCustomName($n1));
	    $inventory->setItem(11, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(12, $this->getItemFactory($id11, $m11, 1)->setCustomName($n11));
	    $inventory->setItem(13, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(14, $this->getItemFactory($id15, $m15, 1)->setCustomName($n15));
	    $inventory->setItem(15, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(16, $this->getItemFactory($id25, $m25, 1)->setCustomName($n25));
	    $inventory->setItem(17, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(18, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(19, $this->getItemFactory($id2, $m2, 1)->setCustomName($n2));
	    $inventory->setItem(20, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(22, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(21, $this->getItemFactory($id10, $m10, 1)->setCustomName($n10));
	    $inventory->setItem(23, $this->getItemFactory($id16, $m16, 1)->setCustomName($n16));
	    $inventory->setItem(24, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(25, $this->getItemFactory($id24, $m24, 1)->setCustomName($n24));
	    $inventory->setItem(26, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(27, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(28, $this->getItemFactory($id3, $m3, 1)->setCustomName($n3));
	    $inventory->setItem(29, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(30, $this->getItemFactory($id9, $m9, 1)->setCustomName($n9));
	    $inventory->setItem(31, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(32, $this->getItemFactory($id17, $m17, 1)->setCustomName($n17));
	    $inventory->setItem(33, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(34, $this->getItemFactory($id23, $m23, 1)->setCustomName($n23));
	    $inventory->setItem(35, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(36, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(37, $this->getItemFactory($id4, $m4, 1)->setCustomName($n4));
	    $inventory->setItem(38, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(39, $this->getItemFactory($id8, $m8, 1)->setCustomName($n8));
	    $inventory->setItem(40, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(41, $this->getItemFactory($id18, $m18, 1)->setCustomName($n18));
	    $inventory->setItem(42, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(43, $this->getItemFactory($id22, $m22, 1)->setCustomName($n22));
	    $inventory->setItem(44, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(45, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(46, $this->getItemFactory($id5, $m5, 1)->setCustomName($n5));
	    $inventory->setItem(47, $this->getItemFactory($id6, $m6, 1)->setCustomName($n6));
	    $inventory->setItem(48, $this->getItemFactory($id7, $m7, 1)->setCustomName($n7));
	    $inventory->setItem(49, $this->getItemFactory(368, 0, 1)->setCustomName(" §cQuit Job "));
	    $inventory->setItem(50, $this->getItemFactory($id19, $m19, 1)->setCustomName($n19));
	    $inventory->setItem(51, $this->getItemFactory($id20, $m20, 1)->setCustomName($n20));
	    $inventory->setItem(52, $this->getItemFactory($id21, $m21, 1)->setCustomName($n21));
	    $inventory->setItem(53, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $this->menu->send($s);
	}
	public function view3(Player $s){
	    $this->menu->getInventory()->clearAll();
	    $this->menu->setListener(InvMenu::readonly());
		$this->menu->setListener(Closure::fromCallable([$this, "v3"]));
        $this->menu->setName("§0(Job | ".$this->data->getNested($s->getName().".jobname").")");
        //id
        $id1 = $this->data->getNested($s->getName().".id1");
        $id2 = $this->data->getNested($s->getName().".id2");
        $id3 = $this->data->getNested($s->getName().".id3");
        $id4 = $this->data->getNested($s->getName().".id4");
        $id5 = $this->data->getNested($s->getName().".id5");
        $id6 = $this->data->getNested($s->getName().".id6");
        $id7 = $this->data->getNested($s->getName().".id7");
        $id8 = $this->data->getNested($s->getName().".id8");
        $id9 = $this->data->getNested($s->getName().".id9");
        $id10 = $this->data->getNested($s->getName().".id10");
        $id11 = $this->data->getNested($s->getName().".id11");
        $id12 = $this->data->getNested($s->getName().".id12");
        $id13 = $this->data->getNested($s->getName().".id13");
        $id14 = $this->data->getNested($s->getName().".id14");
        $id15 = $this->data->getNested($s->getName().".id15");
        $id16 = $this->data->getNested($s->getName().".id16");
        $id17 = $this->data->getNested($s->getName().".id17");
        $id18 = $this->data->getNested($s->getName().".id18");
        $id19 = $this->data->getNested($s->getName().".id19");
        $id20 = $this->data->getNested($s->getName().".id20");
        $id21 = $this->data->getNested($s->getName().".id21");
        $id22 = $this->data->getNested($s->getName().".id22");
        $id23 = $this->data->getNested($s->getName().".id23");
        $id24 = $this->data->getNested($s->getName().".id24");
        $id25 = $this->data->getNested($s->getName().".id25");
        $id26 = $this->data->getNested($s->getName().".id26");
        $id27 = $this->data->getNested($s->getName().".id27");
        //Meta
        $m1 = $this->data->getNested($s->getName().".m1");
        $m2 = $this->data->getNested($s->getName().".m2");
        $m3 = $this->data->getNested($s->getName().".m3");
        $m4 = $this->data->getNested($s->getName().".m4");
        $m5 = $this->data->getNested($s->getName().".m5");
        $m6 = $this->data->getNested($s->getName().".m6");
        $m7 = $this->data->getNested($s->getName().".m7");
        $m8 = $this->data->getNested($s->getName().".m8");
        $m9 = $this->data->getNested($s->getName().".m9");
        $m10 = $this->data->getNested($s->getName().".m10");
        $m11 = $this->data->getNested($s->getName().".m11");
        $m12 = $this->data->getNested($s->getName().".m12");
        $m13 = $this->data->getNested($s->getName().".m13");
        $m14 = $this->data->getNested($s->getName().".m14");
        $m15 = $this->data->getNested($s->getName().".m15");
        $m16 = $this->data->getNested($s->getName().".m16");
        $m17 = $this->data->getNested($s->getName().".m17");
        $m18 = $this->data->getNested($s->getName().".m18");
        $m19 = $this->data->getNested($s->getName().".m19");
        $m20 = $this->data->getNested($s->getName().".m20");
        $m21 = $this->data->getNested($s->getName().".m21");
        $m22 = $this->data->getNested($s->getName().".m22");
        $m23 = $this->data->getNested($s->getName().".m23");
        $m24 = $this->data->getNested($s->getName().".m24");
        $m25 = $this->data->getNested($s->getName().".m25");
        $m26 = $this->data->getNested($s->getName().".m26");
        $m27 = $this->data->getNested($s->getName().".m27");
        //Name
        $n1 = $this->data->getNested($s->getName().".n1");
        $n2 = $this->data->getNested($s->getName().".n2");
        $n3 = $this->data->getNested($s->getName().".n3");
        $n4 = $this->data->getNested($s->getName().".n4");
        $n5 = $this->data->getNested($s->getName().".n5");
        $n6 = $this->data->getNested($s->getName().".n6");
        $n7 = $this->data->getNested($s->getName().".n7");
        $n8 = $this->data->getNested($s->getName().".n8");
        $n9 = $this->data->getNested($s->getName().".n9");
        $n10 = $this->data->getNested($s->getName().".n10");
        $n11 = $this->data->getNested($s->getName().".n11");
        $n12 = $this->data->getNested($s->getName().".n12");
        $n13 = $this->data->getNested($s->getName().".n13");
        $n14 = $this->data->getNested($s->getName().".n14");
        $n15 = $this->data->getNested($s->getName().".n15");
        $n16 = $this->data->getNested($s->getName().".n16");
        $n17 = $this->data->getNested($s->getName().".n17");
        $n18 = $this->data->getNested($s->getName().".n18");
        $n19 = $this->data->getNested($s->getName().".n19");
        $n20 = $this->data->getNested($s->getName().".n20");
        $n21 = $this->data->getNested($s->getName().".n21");
        $n22 = $this->data->getNested($s->getName().".n22");
        $n23 = $this->data->getNested($s->getName().".n23");
        $n24 = $this->data->getNested($s->getName().".n24");
        $n25 = $this->data->getNested($s->getName().".n25");
        $n26 = $this->data->getNested($s->getName().".n26");
        $n27 = $this->data->getNested($s->getName().".n27");
	    $inventory = $this->menu->getInventory();
	    
	    $inventory->setItem(0, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(1, $this->getItemFactory(257, 0, 1)->setCustomName(" §eMiner "));
	    $inventory->setItem(2, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(3, $this->getItemFactory($id12, $m12, 1)->setCustomName($n12));
	    $inventory->setItem(4, $this->getItemFactory($id13, $m13, 1)->setCustomName($n13));
	    $inventory->setItem(5, $this->getItemFactory($id14, $m14, 1)->setCustomName($n14));
	    $inventory->setItem(6, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(7, $this->getItemFactory($id26, $m26, 1)->setCustomName($n26));
	    $inventory->setItem(8, $this->getItemFactory($id27, $m27, 1)->setCustomName($n27));
	    $inventory->setItem(9, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(10, $this->getItemFactory($id1, $m1, 1)->setCustomName($n1));
	    $inventory->setItem(11, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(12, $this->getItemFactory($id11, $m11, 1)->setCustomName($n11));
	    $inventory->setItem(13, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(14, $this->getItemFactory($id15, $m15, 1)->setCustomName($n15));
	    $inventory->setItem(15, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(16, $this->getItemFactory($id25, $m25, 1)->setCustomName($n25));
	    $inventory->setItem(17, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(18, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(19, $this->getItemFactory($id2, $m2, 1)->setCustomName($n2));
	    $inventory->setItem(20, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(22, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(21, $this->getItemFactory($id10, $m10, 1)->setCustomName($n10));
	    $inventory->setItem(23, $this->getItemFactory($id16, $m16, 1)->setCustomName($n16));
	    $inventory->setItem(24, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(25, $this->getItemFactory($id24, $m24, 1)->setCustomName($n24));
	    $inventory->setItem(26, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(27, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(28, $this->getItemFactory($id3, $m3, 1)->setCustomName($n3));
	    $inventory->setItem(29, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(30, $this->getItemFactory($id9, $m9, 1)->setCustomName($n9));
	    $inventory->setItem(31, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(32, $this->getItemFactory($id17, $m17, 1)->setCustomName($n17));
	    $inventory->setItem(33, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(34, $this->getItemFactory($id23, $m23, 1)->setCustomName($n23));
	    $inventory->setItem(35, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(36, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(37, $this->getItemFactory($id4, $m4, 1)->setCustomName($n4));
	    $inventory->setItem(38, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(39, $this->getItemFactory($id8, $m8, 1)->setCustomName($n8));
	    $inventory->setItem(40, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(41, $this->getItemFactory($id18, $m18, 1)->setCustomName($n18));
	    $inventory->setItem(42, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(43, $this->getItemFactory($id22, $m22, 1)->setCustomName($n22));
	    $inventory->setItem(44, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(45, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(46, $this->getItemFactory($id5, $m5, 1)->setCustomName($n5));
	    $inventory->setItem(47, $this->getItemFactory($id6, $m6, 1)->setCustomName($n6));
	    $inventory->setItem(48, $this->getItemFactory($id7, $m7, 1)->setCustomName($n7));
	    $inventory->setItem(49, $this->getItemFactory(368, 0, 1)->setCustomName(" §cQuit Job "));
	    $inventory->setItem(50, $this->getItemFactory($id19, $m19, 1)->setCustomName($n19));
	    $inventory->setItem(51, $this->getItemFactory($id20, $m20, 1)->setCustomName($n20));
	    $inventory->setItem(52, $this->getItemFactory($id21, $m21, 1)->setCustomName($n21));
	    $inventory->setItem(53, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $this->menu->send($s);
	}
	public function view4(Player $s){
	    $this->menu->getInventory()->clearAll();
	    $this->menu->setListener(InvMenu::readonly());
		$this->menu->setListener(Closure::fromCallable([$this, "v4"]));
        $this->menu->setName("§0(Job | ".$this->data->getNested($s->getName().".jobname").")");
        //id
        $id1 = $this->data->getNested($s->getName().".id1");
        $id2 = $this->data->getNested($s->getName().".id2");
        $id3 = $this->data->getNested($s->getName().".id3");
        $id4 = $this->data->getNested($s->getName().".id4");
        $id5 = $this->data->getNested($s->getName().".id5");
        $id6 = $this->data->getNested($s->getName().".id6");
        $id7 = $this->data->getNested($s->getName().".id7");
        $id8 = $this->data->getNested($s->getName().".id8");
        $id9 = $this->data->getNested($s->getName().".id9");
        $id10 = $this->data->getNested($s->getName().".id10");
        $id11 = $this->data->getNested($s->getName().".id11");
        $id12 = $this->data->getNested($s->getName().".id12");
        $id13 = $this->data->getNested($s->getName().".id13");
        $id14 = $this->data->getNested($s->getName().".id14");
        $id15 = $this->data->getNested($s->getName().".id15");
        $id16 = $this->data->getNested($s->getName().".id16");
        $id17 = $this->data->getNested($s->getName().".id17");
        $id18 = $this->data->getNested($s->getName().".id18");
        $id19 = $this->data->getNested($s->getName().".id19");
        $id20 = $this->data->getNested($s->getName().".id20");
        $id21 = $this->data->getNested($s->getName().".id21");
        $id22 = $this->data->getNested($s->getName().".id22");
        $id23 = $this->data->getNested($s->getName().".id23");
        $id24 = $this->data->getNested($s->getName().".id24");
        $id25 = $this->data->getNested($s->getName().".id25");
        $id26 = $this->data->getNested($s->getName().".id26");
        $id27 = $this->data->getNested($s->getName().".id27");
        //Meta
        $m1 = $this->data->getNested($s->getName().".m1");
        $m2 = $this->data->getNested($s->getName().".m2");
        $m3 = $this->data->getNested($s->getName().".m3");
        $m4 = $this->data->getNested($s->getName().".m4");
        $m5 = $this->data->getNested($s->getName().".m5");
        $m6 = $this->data->getNested($s->getName().".m6");
        $m7 = $this->data->getNested($s->getName().".m7");
        $m8 = $this->data->getNested($s->getName().".m8");
        $m9 = $this->data->getNested($s->getName().".m9");
        $m10 = $this->data->getNested($s->getName().".m10");
        $m11 = $this->data->getNested($s->getName().".m11");
        $m12 = $this->data->getNested($s->getName().".m12");
        $m13 = $this->data->getNested($s->getName().".m13");
        $m14 = $this->data->getNested($s->getName().".m14");
        $m15 = $this->data->getNested($s->getName().".m15");
        $m16 = $this->data->getNested($s->getName().".m16");
        $m17 = $this->data->getNested($s->getName().".m17");
        $m18 = $this->data->getNested($s->getName().".m18");
        $m19 = $this->data->getNested($s->getName().".m19");
        $m20 = $this->data->getNested($s->getName().".m20");
        $m21 = $this->data->getNested($s->getName().".m21");
        $m22 = $this->data->getNested($s->getName().".m22");
        $m23 = $this->data->getNested($s->getName().".m23");
        $m24 = $this->data->getNested($s->getName().".m24");
        $m25 = $this->data->getNested($s->getName().".m25");
        $m26 = $this->data->getNested($s->getName().".m26");
        $m27 = $this->data->getNested($s->getName().".m27");
        //Name
        $n1 = $this->data->getNested($s->getName().".n1");
        $n2 = $this->data->getNested($s->getName().".n2");
        $n3 = $this->data->getNested($s->getName().".n3");
        $n4 = $this->data->getNested($s->getName().".n4");
        $n5 = $this->data->getNested($s->getName().".n5");
        $n6 = $this->data->getNested($s->getName().".n6");
        $n7 = $this->data->getNested($s->getName().".n7");
        $n8 = $this->data->getNested($s->getName().".n8");
        $n9 = $this->data->getNested($s->getName().".n9");
        $n10 = $this->data->getNested($s->getName().".n10");
        $n11 = $this->data->getNested($s->getName().".n11");
        $n12 = $this->data->getNested($s->getName().".n12");
        $n13 = $this->data->getNested($s->getName().".n13");
        $n14 = $this->data->getNested($s->getName().".n14");
        $n15 = $this->data->getNested($s->getName().".n15");
        $n16 = $this->data->getNested($s->getName().".n16");
        $n17 = $this->data->getNested($s->getName().".n17");
        $n18 = $this->data->getNested($s->getName().".n18");
        $n19 = $this->data->getNested($s->getName().".n19");
        $n20 = $this->data->getNested($s->getName().".n20");
        $n21 = $this->data->getNested($s->getName().".n21");
        $n22 = $this->data->getNested($s->getName().".n22");
        $n23 = $this->data->getNested($s->getName().".n23");
        $n24 = $this->data->getNested($s->getName().".n24");
        $n25 = $this->data->getNested($s->getName().".n25");
        $n26 = $this->data->getNested($s->getName().".n26");
        $n27 = $this->data->getNested($s->getName().".n27");
	    $inventory = $this->menu->getInventory();
	    
	    $inventory->setItem(0, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(1, $this->getItemFactory(42, 0, 1)->setCustomName(" §eBuilder "));
	    $inventory->setItem(2, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(3, $this->getItemFactory($id12, $m12, 1)->setCustomName($n12));
	    $inventory->setItem(4, $this->getItemFactory($id13, $m13, 1)->setCustomName($n13));
	    $inventory->setItem(5, $this->getItemFactory($id14, $m14, 1)->setCustomName($n14));
	    $inventory->setItem(6, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(7, $this->getItemFactory($id26, $m26, 1)->setCustomName($n26));
	    $inventory->setItem(8, $this->getItemFactory($id27, $m27, 1)->setCustomName($n27));
	    $inventory->setItem(9, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(10, $this->getItemFactory($id1, $m1, 1)->setCustomName($n1));
	    $inventory->setItem(11, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(12, $this->getItemFactory($id11, $m11, 1)->setCustomName($n11));
	    $inventory->setItem(13, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(14, $this->getItemFactory($id15, $m15, 1)->setCustomName($n15));
	    $inventory->setItem(15, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(16, $this->getItemFactory($id25, $m25, 1)->setCustomName($n25));
	    $inventory->setItem(17, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(18, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(19, $this->getItemFactory($id2, $m2, 1)->setCustomName($n2));
	    $inventory->setItem(20, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(22, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(21, $this->getItemFactory($id10, $m10, 1)->setCustomName($n10));
	    $inventory->setItem(23, $this->getItemFactory($id16, $m16, 1)->setCustomName($n16));
	    $inventory->setItem(24, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(25, $this->getItemFactory($id24, $m24, 1)->setCustomName($n24));
	    $inventory->setItem(26, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(27, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(28, $this->getItemFactory($id3, $m3, 1)->setCustomName($n3));
	    $inventory->setItem(29, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(30, $this->getItemFactory($id9, $m9, 1)->setCustomName($n9));
	    $inventory->setItem(31, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(32, $this->getItemFactory($id17, $m17, 1)->setCustomName($n17));
	    $inventory->setItem(33, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(34, $this->getItemFactory($id23, $m23, 1)->setCustomName($n23));
	    $inventory->setItem(35, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(36, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(37, $this->getItemFactory($id4, $m4, 1)->setCustomName($n4));
	    $inventory->setItem(38, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(39, $this->getItemFactory($id8, $m8, 1)->setCustomName($n8));
	    $inventory->setItem(40, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(41, $this->getItemFactory($id18, $m18, 1)->setCustomName($n18));
	    $inventory->setItem(42, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(43, $this->getItemFactory($id22, $m22, 1)->setCustomName($n22));
	    $inventory->setItem(44, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(45, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(46, $this->getItemFactory($id5, $m5, 1)->setCustomName($n5));
	    $inventory->setItem(47, $this->getItemFactory($id6, $m6, 1)->setCustomName($n6));
	    $inventory->setItem(48, $this->getItemFactory($id7, $m7, 1)->setCustomName($n7));
	    $inventory->setItem(49, $this->getItemFactory(368, 0, 1)->setCustomName(" §cQuit Job "));
	    $inventory->setItem(50, $this->getItemFactory($id19, $m19, 1)->setCustomName($n19));
	    $inventory->setItem(51, $this->getItemFactory($id20, $m20, 1)->setCustomName($n20));
	    $inventory->setItem(52, $this->getItemFactory($id21, $m21, 1)->setCustomName($n21));
	    $inventory->setItem(53, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $this->menu->send($s);
	}
	//Transaction
	public function v1(InvMenuTransaction $transaction): InvMenuTransactionResult {
	    $sender = $transaction->getPlayer();
        $inventory = $transaction->getAction()->getInventory();
        $action = $transaction->getAction();
        $item = $transaction->getItemClicked();
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        if($item->getName() == " §cQuit Job "){
            $this->data->setNested($sender->getName().".has", false);
            $this->data->setNested($sender->getName().".job", "");
            $this->data->setNested($sender->getName().".jobname", "");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 0);
            $this->data->setNested($sender->getName().".level", 0);
            $this->data->setNested($sender->getName().".uang", 0);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Quit Job");
        }
        if($item->getName() == " §eWoodCutter "){
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix . "§bBreak a wood or log");
        }
        return $transaction->discard();
	}
	public function v2(InvMenuTransaction $transaction): InvMenuTransactionResult {
	    $sender = $transaction->getPlayer();
        $inventory = $transaction->getAction()->getInventory();
        $action = $transaction->getAction();
        $item = $transaction->getItemClicked();
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        if($item->getName() == " §cQuit Job "){
            $this->data->setNested($sender->getName().".has", false);
            $this->data->setNested($sender->getName().".job", "");
            $this->data->setNested($sender->getName().".jobname", "");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 0);
            $this->data->setNested($sender->getName().".level", 0);
            $this->data->setNested($sender->getName().".uang", 0);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Quit Job");
        }
        if($item->getName() == " §eButcher "){
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix . "§bKill a mobs");
        }
        return $transaction->discard();
	}
	public function v3(InvMenuTransaction $transaction): InvMenuTransactionResult {
	    $sender = $transaction->getPlayer();
        $inventory = $transaction->getAction()->getInventory();
        $action = $transaction->getAction();
        $item = $transaction->getItemClicked();
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        if($item->getName() == " §cQuit Job "){
            $this->data->setNested($sender->getName().".has", false);
            $this->data->setNested($sender->getName().".job", "");
            $this->data->setNested($sender->getName().".jobname", "");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 0);
            $this->data->setNested($sender->getName().".level", 0);
            $this->data->setNested($sender->getName().".uang", 0);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Quit Job");
        }
        if($item->getName() == " §eMiner "){
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix . "§bBreak a stone and more");
        }
        return $transaction->discard();
	}
	public function v4(InvMenuTransaction $transaction): InvMenuTransactionResult {
	    $sender = $transaction->getPlayer();
        $inventory = $transaction->getAction()->getInventory();
        $action = $transaction->getAction();
        $item = $transaction->getItemClicked();
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        if($item->getName() == " §cQuit Job "){
            $this->data->setNested($sender->getName().".has", false);
            $this->data->setNested($sender->getName().".job", "");
            $this->data->setNested($sender->getName().".jobname", "");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 0);
            $this->data->setNested($sender->getName().".level", 0);
            $this->data->setNested($sender->getName().".uang", 0);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Quit Job");
        }
        if($item->getName() == " §eBuilder "){
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix . "§bPlace a block");
        }
        return $transaction->discard();
	}
	//JobMenu
	public function Introduce($sender)
	{
		$this->menus->setListener(InvMenu::readonly());
	    $this->menus->setListener(Closure::fromCallable([$this, "c1"]));
        $this->menus->setName("§0(Job | Menu)");
	    $inventory = $this->menus->getInventory();
	    
	    $inventory->setItem(0, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(1, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(2, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(3, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(4, $this->getItemFactory(399, 0, 1)->setCustomName(" This Job GUI ")->setLore([" select your job "]));
	    $inventory->setItem(5, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(6, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(7, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(8, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(9, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(10, $this->getItemFactory(258, 0, 1)->setCustomName("§aWoodCutter"));
	    $inventory->setItem(11, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(12, $this->getItemFactory(267, 0, 1)->setCustomName("§aButcher"));
	    $inventory->setItem(13, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(14, $this->getItemFactory(257, 0, 1)->setCustomName("§aMiner"));
	    $inventory->setItem(15, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(16, $this->getItemFactory(42, 0, 1)->setCustomName("§aBuilder"));
	    $inventory->setItem(17, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(18, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(19, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(20, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(21, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(22, $this->getItemFactory(152, 0, 1)->setCustomName(" §l§cBack "));
	    $inventory->setItem(23, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(24, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(25, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $inventory->setItem(26, $this->getItemFactory(101, 0, 1)->setCustomName(" §r §7- §r "));
	    $this->menus->send($sender);
	}
	public function c1(InvMenuTransaction $transaction): InvMenuTransactionResult {
	    $sender = $transaction->getPlayer();
        $inventory = $transaction->getAction()->getInventory();
        $action = $transaction->getAction();
        $item = $transaction->getItemClicked();
        $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        if($item->getName() == "§aWoodCutter"){
            $this->data->setNested($sender->getName().".has", true);
            $this->data->setNested($sender->getName().".job", "job1");
            $this->data->setNested($sender->getName().".jobname", "WoodCutter");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 50);
            $this->data->setNested($sender->getName().".level", 1);
            $this->data->setNested($sender->getName().".uang", 1000);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Join Job ".$this->data->getNested($sender->getName().".jobname"));
        }
        if($item->getName() == "§aButcher"){
            $this->data->setNested($sender->getName().".has", true);
            $this->data->setNested($sender->getName().".job", "job2");
            $this->data->setNested($sender->getName().".jobname", "Butcher");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 20);
            $this->data->setNested($sender->getName().".level", 1);
            $this->data->setNested($sender->getName().".uang", 1500);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Join Job ".$this->data->getNested($sender->getName().".jobname"));
        }
        if($item->getName() == "§aMiner"){
            $this->data->setNested($sender->getName().".has", true);
            $this->data->setNested($sender->getName().".job", "job3");
            $this->data->setNested($sender->getName().".jobname", "Miner");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 100);
            $this->data->setNested($sender->getName().".level", 1);
            $this->data->setNested($sender->getName().".uang", 1800);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Join Job ".$this->data->getNested($sender->getName().".jobname"));
        }
        if($item->getName() == "§aBuilder"){
            $this->data->setNested($sender->getName().".has", true);
            $this->data->setNested($sender->getName().".job", "job4");
            $this->data->setNested($sender->getName().".jobname", "Builder");
            $this->data->setNested($sender->getName().".load", 0);
            $this->data->setNested($sender->getName().".max", 150);
            $this->data->setNested($sender->getName().".level", 1);
            $this->data->setNested($sender->getName().".uang", 2000);
            $this->data->save();
            $sender->removeCurrentWindow();
            $sender->sendMessage($this->prefix."§aSukses Join Job ".$this->data->getNested($sender->getName().".jobname"));
        }
        if($item->getName() == "§l§cBack"){
            $sender->removeCurrentWindow();
            $sender->sendMessage("§aThanks for visit");
        }
        return $transaction->discard();
    }
}

class Sys extends Task {
	 
	 public function __construct($plugin){
	 	 $this->plugin = $plugin;
	 }
	 
	 public function onRun(): void{
	 	 
	 	 foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
	 	     $this->plugin->system2($player);
	 	     $this->plugin->system3($player);
	 	     $this->plugin->system4($player);
	 	 }
	 }
}