<?php

namespace Bfg\Comcode;

class Comcode
{
    /**
     * @param  string  $file
     * @param  array  $sources
     * @return string|null
     */
    public static function findFile(
        string $file,
        array $sources = [null, 'base_path']
    ): ?string {
        if (! is_file($file)) {
            foreach ($sources as $source) {
                $newFile = $source ? $source($file) : __DIR__ . '/' . $file;
                if (is_file($newFile)) {
                    return $newFile;
                }
            }
            return null;
        }
        return ! str_contains($file, DIRECTORY_SEPARATOR)
            ? getcwd() . '/' . $file
            : $file;
    }

    /**
     * @param  string  $file
     * @return string
     */
    public static function fileReservation(string $file): string
    {
        $prefix = getcwd() . '/';

        if (! str_starts_with($file, $prefix)) {

            $file = $prefix . $file;
        }

        if ($findFile = static::findFile($file)) {

            return $findFile;
        }

        file_put_contents($file, "<?php\n\n");

        return $file;
    }

    /**
     * @param  string  $class
     * @return string
     */
    public static function classNamespaceName(string $class): string
    {
        return implode(
            "\\", array_slice(explode("\\", $class), 0, -1)
        );
    }
}
