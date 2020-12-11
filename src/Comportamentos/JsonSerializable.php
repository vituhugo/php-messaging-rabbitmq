<?php


namespace Mensageria\Comportamentos;


interface JsonSerializable
{
    public function jsonSerialize($recursiveLevel = 1);
}