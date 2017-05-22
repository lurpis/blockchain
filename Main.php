<?php
/**
 * Create by lurrpis
 * Date 21/05/2017 10:27 PM
 * Blog lurrpis.com
 */

include_once 'Block.php';

class Main
{
    const QUERY_LATEST = 0;
    const QUERY_ALL = 1;
    const RESPONSE_BLOCKCHAIN = 2;

    public $blockchain = [];

    public function initBlock()
    {
        $this->blockchain[] = $this->getGenesisBlock();
    }

    public function getGenesisBlock()
    {
        return new Block(0, 0, 1495377189, '创世块', 'cf33939a229b82d4dd6a40e9b5f1ea9c66572ccf2d31af7617b309b990bfa25a');
    }

    public function calculateHash($index, $previousHash, $timestamp, $data)
    {
        return hash_hmac('sha256', $index . $previousHash . $timestamp . $data, 'pingpp');
    }

    public function generateNextBlock($blockData)
    {
        $previousBlock = $this->getLatestBlock();
        $nextIndex = $previousBlock->index + 1;
        $nextTimestamp = time();
        $nextHash = $this->calculateHash($nextIndex, $previousBlock->hash, $nextTimestamp, $blockData);

        return new Block($nextIndex, $previousBlock->hash, $nextTimestamp, $blockData, $nextHash);
    }

    public function getLatestBlock()
    {
        return array_pop($this->blockchain);
    }

    public function isValidNewBlock($newBlock, $previousBlock)
    {
        if ($previousBlock->index + 1 !== $newBlock->index) {
            echo 'invalid index';

            return false;
        } else if ($previousBlock->hash !== $newBlock->previousHash) {
            echo 'invalid previous hash';

            return false;
        } else if ($this->calculateHashForBlock($newBlock) !== $newBlock->hash) {
            echo 'invalid hash: ' . $this->calculateHashForBlock($newBlock) . ' ' . $newBlock->hash;

            return false;
        }

        return true;
    }

    public function replaceChain($newBlocks)
    {
        if ($this->isValidChain($newBlocks) && count($newBlocks) > count($this->blockchain)) {
            echo 'Received blockchain is valid. Replacing current blockchain with received blockchain';
            $this->blockchain = $newBlocks;
            $this->broadcast($this->responseLatestMsg());
        } else {
            echo 'Received blockchain invalid';
        }
    }

    public function isValidChain($blockchainToValidate)
    {
        if (json_encode($blockchainToValidate[0]) !== json_encode($this->getGenesisBlock())) {
            return false;
        }
        $tempBlocks = [$blockchainToValidate[0]];
        for ($i = 1; $i < count($blockchainToValidate); $i ++) {
            if ($this->isValidNewBlock($blockchainToValidate[$i], $tempBlocks[$i - 1])) {
                array_push($tempBlocks, $blockchainToValidate[$i]);
            } else {
                return false;
            }
        }

        return true;
    }

    public function responseLatestMsg()
    {
        return json_encode([
            'type' => static::RESPONSE_BLOCKCHAIN,
            'data' => json_encode($this->getLatestBlock())
        ]);
    }

    public function broadcast($message)
    {

    }

    public function calculateHashForBlock($block)
    {
        return $this->calculateHash($block->index, $block->previousHash, $block->timestamp, $block->data);
    }
}