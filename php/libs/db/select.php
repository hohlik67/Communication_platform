<?php
	
	function sel($sql, $data='no') {
        
        global $pdo;
        
        if($data !== 'no') {
            
            $statement = $pdo->prepare($sql);
            $statement->execute($data);
            $check = 0;
            
            foreach ($statement as $row) {
                yield $row;
                if(!$check) {
                    $check = 1;
                }
            }
            
            if(!$check) {
                yield 'no';
            }
            
        } else {
            
            $query = $pdo -> query($sql);
            $check = 0;
            
            foreach ($query as $row) {
                yield $row;
                if(!$check) {
                    $check = 1;
                }
            }
            
            if(!$check) {
                yield 'no';
            }
        }
	}

?>