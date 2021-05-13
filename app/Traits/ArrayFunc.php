<?php

namespace App\Traits;

trait ArrayFunc
{
    /**
     * @param array $array
     * @param string $key
     * @param string $value
     * @param bool $single
     * @return array
     */
    function search_key_val ($array, string $key, string $value, $single = false)
    {
        $results = [];

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                if ($single === true) {
                    return $array;
                }
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $innerSearch = $this->search_key_val($subarray, $key, $value, $single);
                if ($single === true && !empty($innerSearch)) {
                    return $innerSearch;
                }
                $results = array_merge($results, $innerSearch);
            }
        }

        return $results;
    }
}
