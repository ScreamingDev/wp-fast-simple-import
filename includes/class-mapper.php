<?php

namespace WP_FSI;

class Mapping extends \ArrayObject {
	function __invoke( $data ) {
		return $this->apply( $data );
	}

	public function apply( $data ) {
		$output = new \ArrayObject();

		foreach ( $this->getArrayCopy() as $key => $value ) {
			if ( is_scalar( $value ) ) {
				// This is some simple data so at first we store it directly in the output.
				$output[ $key ] = $value;
				
				// Check if it was meant as an index hash.
				if ( is_array( $data ) && isset( $data[ $value ] ) ) {
					// Data has been found in an array so we fetch it from there.
					$output[ $key ] = $data[ $value ];
				} elseif ( is_object( $data ) && isset( $data->$value ) ) {
					// Data has been found in an object so we fetch it from there.
					$output[ $key ] = $data->$value;
				}

				continue;
			}

			if ( is_callable( $value ) ) {
				// Found a callable that wants to handle the data.
				$output[ $key ] = $value( $this, $data, $output );
				
				continue;
			}
			
			// Unknown what came in so we throw an exception.
			throw new \InvalidArgumentException(
				sprintf(
					'Unhandled type of mapping for "%s": %s. Please use a string or a callable.',
					$key,
					gettype( $value )
				)
			);
		}

		return (array) $output;
	}

}
