<?php

namespace ComposerIncludeFiles;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\IO\IOInterface;
use ComposerIncludeFiles\Composer\AutoloadGenerator;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \ComposerIncludeFiles\Composer\AutoloadGenerator
     */
    protected $generator;

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->generator = new AutoloadGenerator($composer->getEventDispatcher(), $io);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return ['post-autoload-dump' => 'dumpFiles'];
    }

    public function deactivate()
    {
    }

    public function uninstall()
    {
    }
    
    public function dumpFiles()
    {
        $extraConfig = $this->composer->getRepositoryManager()->findPackage("orbitali/core", "*")->getExtra();
        if (!array_key_exists('include_files', $extraConfig) || !is_array($extraConfig['include_files'])) {
            return;
        }

        $extraConfig['include_files'] = array_map(function ($item) {
            return __DIR__ . "/../../core/" . $item;
        }, $extraConfig["include_files"]);

        $this->generator->dumpFiles($this->composer, $extraConfig['include_files']);

    }
}
