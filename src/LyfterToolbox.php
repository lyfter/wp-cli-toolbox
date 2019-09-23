<?php


namespace Lyfter\WP_CLI;

use Lyfter\WP_CLI\Commands\Replace;

class LyfterToolbox
{
    /**
     * Calls the lyfter replace command.
     *
     * This command takes two arguments.
     * The first arguments is the value that needs to be searched for.
     * The second argument is the value that we will replace the matching content with
     *
     * Example usage;
     * wp lyfter replace example.lyfter.com example.com
     *
     * @param $args
     */
    public function replace($args) {
        $command = New Replace();
        $command->call($args);
    }
}