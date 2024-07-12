<?php
declare(strict_types=1);

namespace Gaffer;

class Util
{
    /**
     * @param string $text
     * @return string
     */
    public static function trimInternal(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * @param string $class
     * @return array
     * @throws \ReflectionException
     */
    public static function getPropertyAnnotations(string $class): array
    {
        $reflect = new \ReflectionClass($class);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $annotationsByPropertyName = [];
        foreach($properties as $property) {
            if ($property->isStatic()) continue;
            $annotations = array_filter(array_map(
                function ($fullComment) {
                    $trimmed = self::trimInternal($fullComment);
                    $comment = ltrim($trimmed,'/* ');
                    if (str_starts_with($comment, '@')) {
                        return substr($comment,1);
                    }
                    else {
                        return null;
                    }
                },
                explode(PHP_EOL, $property->getDocComment())
            ));
            $annotationsByPropertyName[$property->getName()] = $annotations;
        }
        return $annotationsByPropertyName;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function camelCaseToSnakeCase(string $str): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }
}