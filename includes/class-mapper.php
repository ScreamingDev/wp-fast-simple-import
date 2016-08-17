<?php

namespace WP_FSI;

class Mapping extends \ArrayObject {
	function __invoke( $data ) {
		return $this->apply( $data );
	}

	public function apply( $data ) {
		$map = array();

		foreach ( $this->getArrayCopy() as $key => $value ) {
			if ( is_scalar( $value ) ) {
				if ( is_array( $data ) && isset( $data[ $value ] ) ) {
					$map[ $key ] = $data[ $value ];
				} elseif ( is_object( $data ) && isset( $data->$value ) ) {
					$map[ $key ] = $data->$value;
				}

				continue;
			}

			if ( is_scalar( $value ) ) {
				$map[ $key ] = $value;
				continue;
			}

			if ( is_callable( $value ) ) {
				$map[ $key ] = $value( $data );
			}
		}

		return $map;
	}

}
