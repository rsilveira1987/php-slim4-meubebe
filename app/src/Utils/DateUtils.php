<?php

    namespace App\Utils;

    use App\Exceptions\AppException;
    use DateTime;
    use Exception;
    use ReflectionClass;
    use ReflectionProperty;

    class DateUtils
    {
        public static function isDateTimeValid($d) {
            $d = new DateTime($d);
            $dateTimeInfo = DateTime::getLastErrors();
            if ($dateTimeInfo['warning_count'] == 0 && $dateTimeInfo['error_count'] == 0) return true;
            return false;
        }
    }