<?php

    namespace App\Utils;

    /**
     * Session
     * Registry pattern
     */
    class Session
    {
        public function __construct()
        {
            // create session if not exists
            if (!session_id()) {
                session_start();
            }
        }

        public static function get($key)
        {
           if (isset($_SESSION[$key])) {
               return $_SESSION[$key];
           }
           return null;
        }

        public static function set($key, $value)
        {
            if ($value == null) {
                unset($_SESSION[$key]);
            } else {
                $_SESSION[$key] = $value;
            }
        }

        public static function destroy()
        {
            $_SESSION = [];
            session_destroy();
            // setcookie (session_name( ), '', time()-300);
        }
    }