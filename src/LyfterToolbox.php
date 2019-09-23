<?php


namespace Lyfter\WP_CLI;


use Lyfter\WP_CLI\Commands\Replace;

class LyfterToolbox
{
    public function replace($args) {
        $command = New Replace();
        $command->call($args);
    }
}