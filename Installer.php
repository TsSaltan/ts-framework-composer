<?php

namespace tsframe;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Composer\Script\Event;

class Installer
{
    public static function installFramework(Event $event)
    {
        /**
         * Пути, что откуда и куда перемещать0
         * @var array
         */
        $files = ['./vendor/tssaltan/ts-framework/' => './'];

        /**
         * Имена файлов, которые не будут заменены
         * @var array
         */
        $keepOriginal = ['composer.json', 'ts-config.json', '.htaccess'];

        /**
         * Расширения файлов, которые игнорируются при копировании
         * @var array
         */
        $ignoreExt = ['git', 'gitignore', 'gitattributes', 'pack'];

        $fs = new Filesystem;
        $io = $event->getIO();

        foreach ($files as $from => $to) {
            if (is_dir($from)) {
                $finder = new Finder;
                $finder->files()->ignoreDotFiles(false)->in($from);

                foreach ($finder as $file) {
                    $dest = sprintf('%s/%s', $to, $file->getRelativePathname());

                    try {
                        $e = explode('.', $dest);
                        $ext = end($e);

                        if(in_array($ext, $ignoreExt)){
                            continue;
                        }elseif(in_array(basename($dest), $keepOriginal) && file_exists($dest)){
                            $io->write(sprintf('<comment>[ts-framework] Keep original: %s</comment>', $dest));
                        } else {
                            if(file_exists($dest)){
                               $fs->remove($dest);
                            }
                            $fs->copy($file, $dest);
                            $io->write(sprintf('<info>[ts-framework] Installing: <comment>%s</comment> => %s</info>', $file, $dest));
                        }
                        $fs->remove($file);
                    } catch (IOException $e) {
                        throw new \InvalidArgumentException(sprintf('<error>[ts-framework] Install error on %s: %s</error>', $file->getBaseName(), $e->getMessage()));
                    }
                }

                // $fs->remove($from);
            } 
        
        }
        $io->write(sprintf('<comment>[ts-framework]</comment> Install complete!'));
    }
}
