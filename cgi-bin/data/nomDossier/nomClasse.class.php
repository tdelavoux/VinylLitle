<?php

	namespace data\nomDossier;

	class nomClasse extends \data\Data
	{
		
            public function getAll()
            {
//                $statement = $this->db->prepare(
//                        'SELECT *
//                        FROM user');
                //$statement->bindParam(':param', $param, \PDO::PARAM_INT);
                $statement->execute();
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }

	}

?>


