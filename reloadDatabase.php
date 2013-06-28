<?php
echo "Deleting Database..." . PHP_EOL;
echo shell_exec("php scripts/createBlankDatabase.php");
echo PHP_EOL;
echo PHP_EOL;
echo "Migrating..." . PHP_EOL;
echo PHP_EOL;
echo shell_exec("php ./vendor/robmorgan/phinx/bin/phinx migrate");
echo PHP_EOL . "Finished!" . PHP_EOL;