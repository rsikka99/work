<?php

/**
 * Class Tangent_Model_Abstract
 * A generic model that has the magic methods already defined along with a nice constructor
 * @author "Lee Robert"
 */
abstract class Tangent_Model_Abstract extends stdClass
{

    public function __construct (array $options = null)
    {
        if (is_array($options))
        {
            $this->setOptions($options);
        }
    }

    public function getDebugTableHeaders ()
    {
        $methods = get_class_methods($this);
        $output = "<tr>\n";
        foreach ( $methods as $method )
        {
            if (strpos($method, "get") === 0 && strpos($method, "getDebug") !== 0)
            {
                $output .= "\t<th>" . substr($method, 3) . "</th>\n";
            }
        }
        $output .= "</tr>\n";
        return $output;
    }

    public function getDebugTableRows ()
    {
        $methods = get_class_methods($this);
        $output = "";
        foreach ( $methods as $method )
        {
            if (strpos($method, "get") === 0 && strpos($method, "getDebug") !== 0)
            {
                $value = $this->$method();
                if (isset($value))
                    $output .= "<tr>";
                else
                    $output .= "<tr class='error'>";
                $output .= "<th>" . substr($method, 3) . "</th>";
                
                $output .= "\t<td>";
                if ($value instanceof DateInterval)
                {
                    $output .= $value->format("%s seconds");
                
                }
                else if ($value instanceof Application_Model_Abstract)
                {
                    $output .= "<table border='1'><tbody>" . $value->getDebugTableRows() . "</tbody></table>";
                }
                else if (is_bool($value))
                {
                    if ($value)
                        $output .= "TRUE";
                    else
                        $output .= "FALSE";
                }
                else
                {
                    $output .= $value;
                }
                $output .= "</td>\n</tr>\n";
            
            }
        }
        
        return $output;
    }

    public function __set ($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method))
        {
            throw new Exception("Could not set $name. Property either doesn't exist or is read-only.");
        }
        $this->$method($value);
    }

    public function __get ($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method))
        {
            throw new Exception('The property "' . $name . '" does not exist in "' . get_class($this) . '".');
        }
        return $this->$method();
    }

    public function setOptions (array $options)
    {
        $methods = get_class_methods($this);
        foreach ( $options as $key => $value )
        {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods))
            {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * This takes an array from the database and converts it by a set standard
     * this_column_name becomes ThisColumnName
     *
     * @param $options array           
     * @return Tangent_Model_Abstract
     */
    public function setOptionsFromDb ($options)
    {
        $methods = get_class_methods($this);
        foreach ( $options as $key => $value )
        {
            $method = 'set' . str_replace(" ", "", ucwords(str_replace("_", " ", $key)));
            if (in_array($method, $methods))
            {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function echoAll ()
    {
        echo "<pre style='font-size: 14px;'>";
        $methods = get_class_methods($this);
        echo "Instance of " . get_class($this) . "\n";
        foreach ( $methods as $method )
        {
            if (strpos($method, "get") === 0)
            {
                $stringValue = "";
                $key = substr($method, 3);
                $value = $this->$method();
                switch ($key)
                {
                    case "MPSMonitorInterval" :
                        $stringValue = "Set but cannot be shown as string";
                        break;
                    default :
                        $stringValue = $value;
                }
                
                echo "\t";
                if (isset($value))
                {
                    echo "<strong>";
                }
                echo "$key => ";
                
                echo $stringValue;
                
                if (is_array($value))
                {
                    echo " =>";
                    foreach ( $value as $subkey => $subvalue )
                    {
                        echo "\n\t\t";
                        echo "$subkey => ";
                        echo "$subvalue";
                    }
                }
                echo "\n";
                if (isset($value))
                {
                    echo "</strong>";
                }
            }
        
        }
        echo "</pre>";
    }

    public function __tostring ()
    {
        return get_class($this);
    }
}
?>
