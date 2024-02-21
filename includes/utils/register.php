<?php

namespace Brikk\Includes\Utils;

class Register {

    use \Brikk\Includes\Src\Traits\Singleton;

    function __construct() {
        // ..
    }

    public function register( $name, $inst = false ) {

		$this->classes[ $name ] = $inst;

	}

    public function __call( $method, $params ) {

        if ( isset( $this->classes[ $method ] ) ) {
            return $this->classes[ $method ];
        }

        return;

    }

}
