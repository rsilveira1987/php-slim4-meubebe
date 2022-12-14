<?php

    namespace App\Models;

    use App\Exceptions\AppException;
    use App\Utils\DateUtils;
    use DateTime;

    abstract class BaseModel
    {
        protected $id;
        protected $uuid;
        protected $created_at;
        protected $updated_at;

        public function __get($prop) {
			// normalizar
			$prop = strtolower($prop);

            if(!in_array($prop,$this->getProperties())){
                $class = get_called_class();
                throw new AppException("Invalid property: $class->$prop" );
            }

			// verifica se existe um getter no objeto concreto
			if (method_exists($this,'get'.$prop)) {
				// executa o metodo get_<propriedade>
				return call_user_func([$this,'get'.$prop]);
			}

            return $this->$prop;
            
		}

        public function __set($prop, $value) {
			// normalize
			$prop = strtolower($prop);

            if(!in_array($prop,$this->getProperties())){
                $class = get_called_class();
                throw new AppException("Invalid property: $class->$prop" );
            }

			// verifica se existe um setter no objeto concreto
			if (method_exists($this, 'set'. $prop)) {
				// executa o metodo set_<propriedade>
				call_user_func([$this,'set'.$prop],$value);
			} else {
				$this->$prop = $value;
			}
		}

        public function getID() {
            return $this->id;
        }

        public function setID($id) {
            $this->id = $id;
        }

        public function getUUID() {
            return $this->uuid;
        }

        public function setUUID($uuid) {
            $this->uuid = $uuid;
        }

        public function getCreated_At() {
            $d = ($this->created_at) ? new DateTime($this->created_at) : null;
            return $d;
        }

        public function setCreated_At($d) {
            if (!DateUtils::isDateTimeValid($d))
                throw new AppException("Invalid created_at date");
            $this->created_at = $d;
        }

        public function getUpdated_At() {
            $d = ($this->updated_at) ? new DateTime($this->updated_at) : null;
            return $d;
        }

        public function setUpdated_At($d) {
            if (!DateUtils::isDateTimeValid($d))
                throw new AppException("Invalid updated_at date");
            $this->updated_at = $d;
        }

        public static function getEntity() {
            $class = get_called_class();	        // obtem o nome da class
            return constant("{$class}::TABLENAME");	// retorna a constante de classe TABLENAME
        }

        public function getProperties() {
            $reflection = new \ReflectionObject($this);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED); // maneira para acessar as propriedades do modelo
			
			$fields = [];
			foreach($properties as $prop) {
				$fields[] = $prop->name;
			}

			return $fields;
        }

        public function toArray() {
            $fields = $this->getProperties();
			$array = [];
			foreach($fields as $field) {
				$array[$field] = $this->$field;
			}
			return $array;
        }

        public static function fromArray(array $data = []) {
            $class = get_called_class();
            $obj = new $class;
			foreach($data as $field => $value) {
				// executa o metodo magico __set para cada item do array
				call_user_func([$obj,'__set'],$field,$value);
			}
            $obj->uuid = UUID_v4();
            return $obj;
		}

        abstract public function validate();
    }