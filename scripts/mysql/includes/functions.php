<?php

function runSQLFile ($filename, mysqli $dbConnection)
{
    $NL = PHP_EOL;
    $statementNumber = 0;
    $sql = file_get_contents($filename);
    
    $sql = str_replace("DELIMITER $$", "", $sql);
    $sql = str_replace("DELIMITER ;", "", $sql);
    $sql = str_replace("$$" . PHP_EOL . PHP_EOL . "$$", ";", $sql);
    
    
    if ($dbConnection->multi_query($sql))
    {
        
        // Close all the results
        do
        {
            $statementNumber++;
            if (FALSE !== ($result = $dbConnection->store_result()))
            {
                $result->close();
            }
            
            if ($dbConnection->more_results())
            {
                if (!$dbConnection->next_result())
                {
                    throw new Exception("File '{$filename}'. {$NL}Statement #{$statementNumber}. {$NL}Mysqli error #{$dbConnection->errno} - {$dbConnection->error}");
                }
            }
        }
        while ( $dbConnection->more_results() );
    }
    else
    {
        throw new Exception("Statement #{$statementNumber}. Mysqli error #{$dbConnection->errno} - {$dbConnection->error}");
    }
}