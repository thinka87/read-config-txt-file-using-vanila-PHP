<?php

namespace SpeqtaTest;

/**
 * ConfigFormatter Class
 *
 * Using this class can build an array tree with string array list
 */
Class ConfigFormatter {
    
    /**
     * Use to store file reader class object
     * @type object
     * @access private
     */
    private $config_file_reader;
    
    /**
     * Use to store string lines in config file as an array
     * @type array
     * @access private
     */  
    private $config_string_list;

    /**
     * Use to set string delimiter to build array tree view
     * @type constant
     * @access private
     */ 
    const DELIMETER = ".";

    function __construct(ConfigFileReader $config_file_reader) {
        $this->config_file_reader = $config_file_reader;
    }
    
    /**
     * Using this function can get configuration list as array tree,
     * @access public
     * @return array
     */
    public function getConfigAsArray() {
        $this->config_string_list = $this->readConfigFile();
        
        if(!$this->config_string_list)
            return false;
        $config_array_list = $this->getOnlyConfiglines();
        return $this->buildArrayTree($config_array_list, self::DELIMETER, true);
    }
    
    /**
     * Call to file readAsStringArray in ConfigFileReader class
     * @access private
     * Returns the file in an array. Each element of the array corresponds to a line in the file,
     * Upon failure returns false.
     * @return array|bool
     */
    private function readConfigFile() {
        return $this->config_file_reader->readAsStringArray();
    }
    
    /**
     * Select string lines which contains configuration
     * @access private
     * @return array
     */
    private function getOnlyConfiglines() {

        $config_array_list = array();

        foreach ($this->config_string_list as $string_line) {

            $explode_array = array_slice(explode(" ", trim($string_line)), -3, 3, false);

            if (count($explode_array) == 3) {
                if ($explode_array[1] == "=") {

                    if ($this->isQuoted($explode_array[2])) {
                        $config_array_list[$explode_array[0]] = str_replace(array('"',"-"), "", $explode_array[2]) ;
                    } elseif (is_numeric($explode_array[2])) {
                        if (strpos($explode_array[2], ".") !== false) {
                            $config_array_list[$explode_array[0]] = filter_var($explode_array[2], FILTER_VALIDATE_FLOAT);
                        } else {
                            $config_array_list[$explode_array[0]] = filter_var($explode_array[2], FILTER_VALIDATE_INT);
                        }
                    } else {
                        $config_array_list[$explode_array[0]] = filter_var($explode_array[2], FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
        }

        return $config_array_list;
    }
    
    /**
     * Check configuration value is double quoted
     * @param string $value
     * @access private
     * @return bool
     */
    private function isQuoted(string $value) {
        if (strlen($value) < 3) {
            return false;
        }

        $first = substr($value, 0, 1);
        $last = substr($value, -1, 1);

        return ($first === '"' && $last === '"');
    }
    
    /**
     * Build tree view using string array list
     * @param array $array
     * @param string $delimiter
     * @param bool $baseval
     * @access private
     * @return array
     */
    private function buildArrayTree($array, $delimiter = '.', $baseval = false) {
        if (!is_array($array))
            return false;
        $divider = '/' . preg_quote($delimiter, '/') . '/';
        $result = array();
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($divider, $key, -1, PREG_SPLIT_NO_EMPTY);
            $childPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $rootArr = &$result;
            foreach ($parts as $part) {
                if (!isset($rootArr[$part])) {
                    $rootArr[$part] = array();
                } elseif (!is_array($rootArr[$part])) {
                    if ($baseval) {
                        $rootArr[$part] = array('__base_val' => $rootArr[$part]);
                    } else {
                        $rootArr[$part] = array();
                    }
                }
                $rootArr = &$rootArr[$part];
            }

            // Add the final part to the structure
            if (empty($rootArr[$childPart])) {
                $rootArr[$childPart] = $val;
            } elseif ($baseval && is_array($rootArr[$childPart])) {
                $rootArr[$childPart]['__base_val'] = $val;
            }
        }
        return $result;
    }

}

?>