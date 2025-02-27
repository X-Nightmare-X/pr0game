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

class Theme
{
    public static $Themes;
    private $THEMESETTINGS;
    private $skininfo;
    private $skin;
    private $template;
    private $customtpls;

    public function __construct()
    {
        $this->skininfo = [];
        $this->skin		= isset($_SESSION['dpath']) ? $_SESSION['dpath'] : DEFAULT_THEME;
        $this->setUserTheme($this->skin);
    }

    public function isHome()
    {
        $this->template		= ROOT_PATH.'styles/home/';
        $this->customtpls	= [];
    }

    public function setUserTheme($Theme)
    {
        if (!file_exists(ROOT_PATH.'styles/theme/'.$Theme.'/style.cfg')) {
            return false;
        }

        $this->skin		= $Theme;
        $this->parseStyleCFG();
        $this->setStyleSettings();
    }

    public function getTheme()
    {
        return './styles/theme/'.$this->skin.'/';
    }

    public function getThemeName()
    {
        return $this->skin;
    }

    public function getTemplatePath()
    {
        return ROOT_PATH.'/styles/templates/'.$this->skin.'/';
    }

    public function isCustomTPL($tpl)
    {
        if (!isset($this->customtpls)) {
            return false;
        }

        return in_array($tpl, $this->customtpls);
    }

    public function parseStyleCFG()
    {
        require(ROOT_PATH.'styles/theme/'.$this->skin.'/style.cfg');
        $this->skininfo		= $Skin;
        $this->customtpls	= (array) $Skin['templates'];
    }

    public function setStyleSettings()
    {
        if (file_exists(ROOT_PATH.'styles/theme/'.$this->skin.'/settings.cfg')) {
            require(ROOT_PATH.'styles/theme/'.$this->skin.'/settings.cfg');
        }

        $this->THEMESETTINGS	= array_merge([
            'PLANET_ROWS_ON_OVERVIEW' => 2,
            'SHORTCUT_ROWS_ON_FLEET1' => 2,
            'COLONY_ROWS_ON_FLEET1' => 2,
            'ACS_ROWS_ON_FLEET1' => 1,
            'TOPNAV_SHORTLY_NUMBER' => 0,
        ], $THEMESETTINGS);
    }

    public function getStyleSettings()
    {
        return $this->THEMESETTINGS;
    }

    public static function getAvalibleSkins()
    {
        if (!isset(self::$Themes)) {
            if (file_exists(ROOT_PATH.'cache/cache.themes.php')) {
                self::$Themes	= unserialize(file_get_contents(ROOT_PATH.'cache/cache.themes.php'));
            } else {
                $Skins	= array_diff(scandir(ROOT_PATH.'styles/theme/'), ['..', '.', '.svn', '.htaccess', 'index.htm']);
                $Themes	= [];
                foreach ($Skins as $Theme) {
                    if (!file_exists(ROOT_PATH.'styles/theme/'.$Theme.'/style.cfg')) {
                        continue;
                    }

                    require(ROOT_PATH.'styles/theme/'.$Theme.'/style.cfg');
                    $Themes[$Theme]	= $Skin['name'];
                }
                file_put_contents(ROOT_PATH.'cache/cache.themes.php', serialize($Themes));
                self::$Themes	= $Themes;
            }
        }
        return self::$Themes;
    }
}
