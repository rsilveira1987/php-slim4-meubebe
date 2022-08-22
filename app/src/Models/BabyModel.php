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

        public function getname() {
            return $this->name;
        }

        public function setname($name) {
            $this->name = $name;
        }

        public function getdescription() {
            return $this->description;
        }

        public function setdescription($description) {
            $this->description = $description;
        }

        public function getgender() {
            return $this->gender;
        }

        public function setgender($gender) {
            if ($gender != BabyGender::MALE && $gender != BabyGender::FEMALE)
                throw new AppException("Invalid gender");
            $this->gender = $gender;
        }

        public function getborn_at() {
            $dt = ($this->born_at) ? new DateTime($this->born_at) : null;
            return $dt;
        }

        public function setborn_at($d) {
            if (!DateUtils::isDateTimeValid($d))
                throw new AppException("Invalid born_at date");
            $this->born_at = $d;
        }

        public function getAge() {
            return 'undefinned';
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

    }

    class BabyGender 
    {
        const MALE = 'M';
        const FEMALE = 'F';
    }