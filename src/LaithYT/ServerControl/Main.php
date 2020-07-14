<?php

namespace LaithYT\ServerControl;

/*  Copyright (2018 - 2020) (C) LaithYoutuber [LaithYT]
 *
 * Plugin By LaithYT , Gihhub: 'https://github.com/laith98'                                                                         
 *                                                                                                      
 *		88  		8855555555	88888889888	888888888888 88			88	 88888888888'8	888888888888'8  
 *		88			88		88		88			88		 88			88	 88		 	88	88			88  
 *		88			88		88		88			88	   	 88			88	 88			88	88			88  
 *		88			88		88		88			88		 88			88	 88			88	88			88  
 *		88			88		88		88			88		 88			88	 88			88	88			88  
 *		88			8855555588		88			88		 8855555555588   8888888855553	88555555555588	
 *		88			88		88		88			88		 88			88	 			88	88			88  
 *		88			88		88		88			88		 88			88	 			88	88			88  
 *		88			88		88		88			88		 88			88				88	88			88 
 *		85      	88		88		88			88		 88			88				88	88			88  
 *		8855555555	88		88	88888889888		88		 88			88   5555555555588	88888888888888  
 *		                                                                                                
 *		Youtube: Laith Youtuber                                                                         
 *		Facebook: Laith A Al Haddad                                                                     
 *		Discord: Laith.97#8167                                                                          
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * DONORS LIST :
 * - LaithYoutuber
 * - no one
 *
 */

use pocketmine\plugin\PluginBase;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

use pocketmine\event\Listener;

use LaithYT\ServerControl\libs\jojoe77777\FormAPI\CustomForm;
use LaithYT\ServerControl\libs\jojoe77777\FormAPI\SimpleForm;
use LaithYT\ServerControl\libs\jojoe77777\FormAPI\ModalForm;

class Main extends PluginBase implements Listener
{
	
	public $cfg;
	
	public function onEnable()
	{
		@mkdir($this->getDataFolder());
		$this->cfg = new Config($this->getDataFolder() . "LOG.yml", Config::YAML);//TODO: LOG FILE FOR SET DATE AND PLAYER RESTART OR STOP AND REASON
	}
	
   /**
	* @parm CommandSender $sender
	* @parm Command $cmd
	* @parm string $label
	* @parm array $args
	* @return bool
	*/	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		switch($cmd->getName()){
			case "sc":
				if($sender instanceof Player){
					if($sender->hasPermission("sc.cmd")){
						if(!isset($args[0])){
							$this->OpenUI($sender);
						} else {
							$sender->sendMessage(TF::RED . "Usage: /sc");
						}
					} else {
						$sender->sendMessage(TF::RED . "you don't have permission to use command");
					}
				} else {
					$sender->sendMessage(TF::RED . "Use The Command In Server");
				}
			break;
		}
		return true;
	}
	
   /**
	* @parm Player $player
	*/
	public function OpenUI(Player $player){
		$form = new SimpleForm(function (Player $player, int $data = null){
			$res = $data;
			if($res === null){
				return true;
			}
			
			switch ($res){
				case 0:
					$this->OpenStopUI($player);
				break;// SOON ADD RESTART
			}
			
		});
		$form->setTitle(TF::AQUA . "ServerControl");
		$form->addButton(TF::RED . "Stop");
		$form->sendToPlayer($player);
        return $form;
	}
	
	
   /**
	* @parm Player $player
	* @return $form
	*/
	public function OpenStopUI(Player $player){
		$form = new CustomForm(function (Player $player, array $data = null){
			if($data === null){
				return true;
			}
			
			if(isset($data[0])){
				$this->addLog($player->getName(), "STOP", $data[0]);
				$this->kickPlayers($data[0]);
				$player->sendMessage(TF::YELLOW . "Server Stopped");
				$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "stop");
			}
			
		});
		$form->setTitle(TF::AQUA . "ServerControl");
		$form->addInput(TF::AQUA . "Reason", "", "");
		$form->sendToPlayer($player);
        return $form;
	}
	
   /**
	* @parm string $playername
	* @parm string $type
	* @parm string $reason
	*/
	public function addLog(string $playername, string $type, string $reason){
		$format = date("d-m-Y") . ":" . $playername . ":" . $type;
		$cfg = $this->cfg;
		$cfg->set($format, $reason);
		$cfg->save();
	}
	
	
   /**
    * @parm string $reason
	*/
	public function kickPlayers(string $reason){
		foreach ($this->getServer()->getOnlinePlayers() as $p){
			$p->kick('[SC] : ' . $reason);
		}
	}
}
