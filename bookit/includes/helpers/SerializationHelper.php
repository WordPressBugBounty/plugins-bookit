<?php

namespace Bookit\Helpers;

/**
 * Bookit Serialization Helper
 */
class SerializationHelper {

	/**
	 * Unserialize a stored value while rejecting anything that isn't a
	 * plain array, guarding against PHP object injection and suppressing
	 * the native warning on malformed input.
	 *
	 * @since 2.6.0.3
	 *
	 * @param mixed $value
	 * @return array|false The unserialized array, or false if $value isn't a
	 *                      string, fails to unserialize, or unserializes to
	 *                      anything containing an object.
	 */
	public static function safe_unserialize( $value ) {
		if ( ! is_string( $value ) ) {
			return false;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$data = @unserialize( $value, array( 'allowed_classes' => false ) );

		if ( ! is_array( $data ) || self::contains_object( $data ) ) {
			return false;
		}

		return $data;
	}

	/**
	 * Recursively check whether a value contains an object, including a
	 * `__PHP_Incomplete_Class` produced by unserializing with
	 * `allowed_classes => false`.
	 *
	 * @since 2.6.0.3
	 *
	 * @param mixed $value
	 * @return bool
	 */
	private static function contains_object( $value ) {
		if ( is_object( $value ) ) {
			return true;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( self::contains_object( $item ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
