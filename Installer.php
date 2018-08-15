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
        $files = ['./vendor/tssaltan/ts-framework/' => './'];
        $ignore = ['composer.json', 'ts-config.json'];

        $fs = new Filesystem;
        $io = $event->getIO();

        foreach ($files as $from => $to) {
            if (is_dir($from)) {
                $finder = new Finder;
                $finder->files()->ignoreDotFiles(false)->in($from);

                foreach ($finder as $file) {
                    $dest = sprintf('%s/%s', $to, $file->getRelativePathname());

                    try {
                        if(in_array(basename($dest), $ignore) && file_exists($dest)){
                            $io->write(sprintf('<comment>[ts-framework]</comment>Keep original <comment>%s</comment>', $dest));
                        } else {
                            if(file_exists($dest)){
                               $fs->remove($dest);
                            }
                            $fs->copy($file, $dest);
                            $io->write(sprintf('<comment>[ts-framework]</comment> Installing <comment>%s</comment> to <comment>%s</comment>.', $file, $dest));
                        }
                        $fs->remove($file);
                    } catch (IOException $e) {
                        throw new \InvalidArgumentException(sprintf('<comment>[ts-framework]</comment> Install error on file <error>%s</error>: $s', $file->getBaseName(), $e->getMessage()));
                    }
                }
                
                $fs->remove($from);
            } 
        
        }
        $io->write(sprintf('<comment>[ts-framework]</comment> Install complete!'));
    }
}
