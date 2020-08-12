<?php

    namespace data;

    abstract class Data
    {
        protected $db;

        protected function getClass()
        {
                $table = get_class($this);
                return substr($table, \strrpos($table, '\\') + 1);
        }

        public function __construct(\PDO $db)
        {
                $this->db = $db;
        }
    }

?>
