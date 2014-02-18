<?php

namespace simbola\core\component\session;

/**
 * Description of Session
 *
 * @author Faraj
 */
class Session extends \simbola\core\component\system\lib\Component {

    public function get($key, $unset = false) {
        if (isset($_SESSION[$key])) {
            $data = $_SESSION[$key];
            if ($unset) {
                $this->set($key, NULL);
            }
            return $data;
        } else {
            return null;
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

}

?>
