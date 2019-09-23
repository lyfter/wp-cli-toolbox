<?php

namespace Lyfter\WP_CLI;

if (! class_exists('WP_CLI')) {
    return;
}


\WP_CLI::add_command('lyfter', LyfterToolbox::class);