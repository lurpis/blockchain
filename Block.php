<?php

/**
 * Create by lurrpis
 * Date 21/05/2017 10:30 PM
 * Blog lurrpis.com
 */
class Block
{
    public $index, $previousHash, $timestamp, $data, $hash;

    public function __construct($index, $previousHash, $timestamp, $data, $hash)
    {
        $this->index = $index;
        $this->previousHash = $previousHash;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->hash = $hash;
    }
}