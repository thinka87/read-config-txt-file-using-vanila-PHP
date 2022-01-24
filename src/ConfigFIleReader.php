<?php

namespace SpeqtaTest;

/**
 * ConfigFileReader Class
 *
 * Using this class can read configuration file contents into an array
 */
Class ConfigFileReader {

    /**
     * To store the file location
     * @type string
     * @access private
     */
    private $file_path;

    function __construct(string $file_path) {
        $this->file_path = $file_path;
    }

    /**
     * Reads an entire file into an array
     * @access public
     * Returns the file in an array. Each element of the array corresponds to a line in the file,
     * with the newline still attached. Upon failure returns false.
     * @return array|bool
     */
    public function readAsStringArray() {
            if (file_exists($this->file_path)) {
                return file($this->file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            } else {
                return false;
            }             
    }

}

?>