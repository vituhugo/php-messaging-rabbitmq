<?php

namespace Mensageria\Conector;


interface ConnectorInterface
{
    public function open();

    public function close();

    public function getChannel();
}