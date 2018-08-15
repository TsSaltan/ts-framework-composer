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
                            $fs->remove($file);
                            $io->write(sprintf('<comment>[ts-framework]</comment>Ignoring <comment>%s</comment>', $from));
                        } else {
                            if(file_exists($dest)){
                               $fs->remove($dest);
                            }
                            $fs->copy($file, $dest);
                            $fs->remove($file);
                            $io->write(sprintf('<comment>[ts-framework]</comment> Installing <comment>%s</comment> to <comment>%s</comment>.', $from, $to));
                        }
                    } catch (IOException $e) {
                        throw new \InvalidArgumentException(sprintf('<error>Could not copy %s</error>', $file->getBaseName()));
                    }
                }
            } 
        
        }
        $io->write(sprintf('<comment>[ts-framework]</comment> Install complete!'));
    }
}
