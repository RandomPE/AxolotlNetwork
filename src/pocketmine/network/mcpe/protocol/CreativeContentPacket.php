<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\inventory\CreativeContentEntry;
use function count;

class CreativeContentPacket extends DataPacket/* implements ClientboundPacket*/{
	public const NETWORK_ID = ProtocolInfo::CREATIVE_CONTENT_PACKET;

	/** @var CreativeContentEntry[] */
	private $entries;

	/**
	 * @param CreativeContentEntry[] $entries
	 */
	public static function create(array $entries) : self{
		$result = new self;
		$result->entries = $entries;
		return $result;
	}

	/** @return CreativeContentEntry[] */
	public function getEntries() : array{ return $this->entries; }

	protected function decodePayload() : void{
		$this->entries = [];
		for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
			$this->entries[] = CreativeContentEntry::read($this);
		}
	}

	protected function encodePayload() : void{
		//TODO
		if($this->protocol < BedrockProtocolInfo::PROTOCOL_1_16_100) {
			$this->entries = [];
		}
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$entry->write($this);
		}
	}

	public function handle(NetworkSession $handler) : bool{
		return $handler->handleCreativeContent($this);
	}
}
