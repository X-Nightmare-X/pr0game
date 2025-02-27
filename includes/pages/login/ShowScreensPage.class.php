<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */


class ShowScreensPage extends AbstractLoginPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $screenshots	= [];
        $directoryIterator = new DirectoryIterator('styles/resource/images/login/screens/');
        foreach ($directoryIterator as $fileInfo) {
            /** @var $fileInfo DirectoryIterator */
            if (!$fileInfo->isFile()) {
                continue;
            }

            $thumbnail = 'styles/resource/images/login/screens/'.$fileInfo->getFilename();
            if (file_exists('styles/resource/images/login/screens/thumbnails/'.$fileInfo->getFilename())) {
                $thumbnail = 'styles/resource/images/login/screens/thumbnails/'.$fileInfo->getFilename();
            }

            $screenshots[]	= [
                'path' 		=> 'styles/resource/images/login/screens/'.$fileInfo->getFilename(),
                'thumbnail' => $thumbnail,
            ];
        }

        $this->assign([
            'screenshots' => $screenshots
        ]);

        $this->display('page.screens.default.tpl');
    }
}
