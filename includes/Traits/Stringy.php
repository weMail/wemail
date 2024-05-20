<?php

namespace WeDevs\WeMail\Traits;

trait Stringy {
    /**
     * Returns a lowercase and trimmed string separated by underscores
     * @param $string
     * @return string
     */
    public function underscored($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    /**
     * Returns an UpperCamelCase version of the supplied string
     * @param $string
     * @return string
     */
    public function upperCamelize($string ) {
        $trimmed = trim( $string );

        $segments = preg_split('/[\s_\-]+/', $trimmed);
        $upperCamelCase = '';
        foreach ($segments as $segment) {
            $upperCamelCase .= ucfirst(strtolower($segment));
        }

        return $upperCamelCase;
    }

    /**
     * Returns a lowercase and trimmed string separated by dashes
     * @param $input
     * @return string
     */
    public function dasherize($input) {
        $trimmed = trim($input);

        $withDashes = preg_replace('/(.)([A-Z])/u', '$1-$2', $trimmed);

        $withDashes = preg_replace('/[\s_]+/', '-', $withDashes);

        return strtolower($withDashes);
    }
}
