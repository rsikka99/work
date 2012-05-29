<?php

function runSQLFile ($filename, mysqli $dbConnection)
{
    $statementNumber = 0;
    $sql = file_get_contents($filename);
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
                    throw new Exception("Statement #{$statementNumber}. Mysqli error #{$dbConnection->errno} - {$dbConnection->error}");
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