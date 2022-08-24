<?php

    namespace App\Models;

    use App\Exceptions\AppException;
    use App\Utils\DateUtils;
    use DateTime;

    class BabyModel extends BaseModel
    {

        const TABLENAME = 'tb_babies';

        protected $name;
        protected $description;
        protected $gender;
        protected $born_at;

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setDescription($description) {
            $this->description = $description;
        }

        public function getGender() {
            return $this->gender;
        }

        public function setGender($gender) {
            if ($gender != BabyGender::MALE && $gender != BabyGender::FEMALE)
                throw new AppException("Invalid gender");
            $this->gender = $gender;
        }

        public function getBorn_At() {
            $dt = ($this->born_at) ? new DateTime($this->born_at) : null;
            return $dt;
        }

        public function setBorn_At($d) {
            if (!DateUtils::isDateTimeValid($d))
                throw new AppException("Invalid born_at date");
            $this->born_at = $d;
        }

        public function isMale() {
            return ($this->gender == BabyGender::MALE) ? true : false;
        }

        public function isFemale() {
            return ($this->gender == BabyGender::FEMALE) ? true : false;
        }

        public function getAge() {
            $years = $this->getAgeInYears();
            $months = ($years > 0) ? $this->getAgeInMonths() % $years : $this->getAgeInMonths();
            // return "{$years} ano(s) e {$months} mes(es)";
            return "{$years} ano(s) e {$months} mes(es)";
        }

        public function getAgeInMonths() {
            if( empty($this->born_at) ) return 'undefined';
            
            $date1 = new DateTime('now');
            $date2 = $this->getborn_at();
            $diff = $date1->diff($date2);

            return ($diff->format('%y') * 12) + $diff->format('%m');
        }

        public function getAgeInYears() {
            if( empty($this->born_at) ) return 'undefined';
            
            $date1 = new DateTime('now');
            $date2 = $this->getborn_at();
            $diff = $date1->diff($date2);

            return $diff->format('%y');
        }

        public function validate() {
            if (empty($this->name)) return false;
            if (empty($this->gender)) return false;
            if (empty($this->born_at)) return false;
            return true;
        }

        public function toJson() {
            return json_encode([
                'id' => intval($this->id),
                'uuid' => intval($this->uuid),
                'name' => $this->name,
                'description' => $this->description,
                'gender' => $this->gender,
                'born_at' => $this->born_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ]);
        }

    }

    class BabyGender 
    {
        const MALE = 'M';
        const FEMALE = 'F';
    }