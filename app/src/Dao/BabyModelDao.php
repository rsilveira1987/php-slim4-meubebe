<?php

    namespace App\Dao;

    use App\Exceptions\AppException;
    use App\Models\BabyModel;
    use App\Models\DB;
    use DateTime;
    use PDOException;

    class BabyModelDao
    {        
        public static function create(array $params) {
            
            $dto = BabyModel::fromArray($params);
            
            //
            // Validate
            //            
            if(!$dto->validate())
                throw new AppException('Invalid object data.');

            // get model entity
			$entity = $dto->getEntity();
			
            // get model data
            $data = $dto->toArray();         
            unset($data['id']); // id is autoincrement
            unset($data['created_at']); // created_at has a default value

			// cria uma instrucao SQL para INSERT
			$colString = implode(', ', array_keys($data));			
			$placeholders = [];
			$values = [];
			foreach ($data as $key => $value) {
				$placeholders[] = ":{$key}";
				$values[":{$key}"] = $value;
			}
			$placeholderString = implode(', ', $placeholders);

			// build sql
			$sql = "INSERT INTO {$entity} ( {$colString} ) VALUES ( {$placeholderString} )";

            try {
                $db = new DB;
                $pdo = $db->connect();
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                
                $id = $pdo->lastInsertId();

                // return object
                return self::retrieveByID($id);
    
            } catch(PDOException $e) {
                die($e->getMessage());
            } finally {
                $db = null;
            }

        }

        public static function retrieveByUUID($uuid) {
            return self::retrieveBy('uuid',$uuid);
        }

        public static function retrieveByID($id) {
            return self::retrieveBy('id',$id);
        }

        public static function retrieveBy($criteria, $value) {
            $model = new BabyModel;
            // get model entity
            $entity = $model->getEntity();

            // build sql
            $sql = "SELECT * FROM {$entity} WHERE {$criteria} = :value";

            try {
                $db = new DB;
                $pdo = $db->connect();
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":value" => $value
                ]);
                
                $object = $stmt->fetchObject(get_class($model));

                return $object;
    
            } catch(PDOException $e) {
                die($e->getMessage());
            } finally {
                $db = null;
            }
        }

        public static function retrieveAll() {
            $model = new BabyModel;
            // get model entity
            $entity = $model->getEntity();

            // build sql
            $sql = "SELECT * FROM {$entity}";

            try {
                $db = new DB;
                $pdo = $db->connect();
                $stmt = $pdo->prepare($sql);
                $stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($model));
                $stmt->execute();
                $objects = $stmt->fetchAll();
    
                return $objects;
            } catch(PDOException $e) {
                die($e->getMessage());
            } finally {
                $db = null;
            }
        }

        public static function update(BabyModel $model) {            
            // get model entity
			$entity = $model->getEntity();
			
            $data = $model->toArray();
			unset($data['id']);
            unset($data['updated_at']);

            // add updated_at
			$now = new DateTime('now');
			$data['updated_at'] = $now->format('Y-m-d H:i:s');
			
			// set key/value placeholders
			$placeholders = [];
			$values = [];
			foreach ($data as $key => $value) {
				$placeholders[] = "{$key} = :{$key}";
				$values[":{$key}"] = $value;
			}
			// add id placeholder
			$values[':id'] = $model->id;
			
			// build sql update string
			$placeholdersList = implode(', ', $placeholders);
			$sql = "UPDATE {$entity} SET {$placeholdersList} WHERE id = :id";

            try {

                if(!$model->validate())
                    throw new AppException('Invalid object data.');

                $db = new DB;
                $pdo = $db->connect();
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                
                // return object
                return self::retrieveByID($model->id);
    
            } catch(PDOException $e) {
                die($e->getMessage());
            } finally {
                $db = null;
            }			
		}

        public static function delete($uuid) {
            // get model entity
            $model = new BabyModel;
            $entity = $model->getEntity();
            

            // build sql
            $sql = "DELETE FROM {$entity} WHERE uuid = :uuid";

            try {
                $db = new DB;
                $pdo = $db->connect();
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uuid' => $uuid
                ]);

                return $stmt->rowCount();
    
            } catch(PDOException $e) {
                die($e->getMessage());
            } finally {
                $db = null;
            }

        }

    }