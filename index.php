#!/usr/bin/env php
<?php
/*
Copyright 2017 Gabriel Hearot
(https://hearot.it)
This file is part of MadelineClient.
MadelineClient is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
MadelineClient is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with MadelineClient.
If not, see <http://www.gnu.org/licenses/>.
*/
try {
    include __DIR__ . '/vendor/autoload.php';
    $parameters = [];
    if (isset($argv[1])) {
        $parameters['type'] = $argv[1];
    } else {
        $parameters['type'] = '';
    }
    if (isset($argv[2])) {
        $parameters['file'] = $argv[2];
    } else {
        $parameters['file'] = '';
    }
    new \hearot\MadelineClient\MadelineClient($parameters);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
__HALT_COMPILER();
