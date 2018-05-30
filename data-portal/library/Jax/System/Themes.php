<?php
/**
 * Themes support for Jax
 * 
 * This class handles the location and loading of themes (css files).
 * It provides methods for listing available themes, saving and retrieving user themes. 
 * 
 * @author Ricardo Assing
 * @copyright (c) Nerds Consulting Limited.
 * @link https://bitbucket.org/cardo/jax-phpcore
 * @package Jax
 */
class Jax_System_Themes
{	
	/**
	 * Path on server to Jax.themes
	 * 
	 * @var string
	 */
	const PUBLIC_PATH = '_assets/js/Jax/Jax/Jax.themes/';
	
	/**
	 * Retrieves a list of themes for Jax client.
	 * 
	 * @param boolean $internal
	 */
	public static function getThemes($internal=false)
	{
		$publicDir = Jax_Config::getPublicPath();
		$themes = array();
		$themeDir = APPLICATION_PATH.'/../'.$publicDir.'/'.self::PUBLIC_PATH;
		$dh = @opendir($themeDir);
		if($dh){
			while (($folder = readdir($dh)) !== false)
			{
				if (!in_array($folder, array('.','..')))
				{
					if (is_dir($themeDir.$folder))
					{
						$dh2 = opendir($themeDir.$folder);
						if($dh2)
						{
							while (($file = readdir($dh2)) !== false)
							{
								if (!in_array($file, array('.','..')))
								{
									$fp = explode(".",$file);
									if(strtolower($fp[count($fp)-1]) == 'css')
									{
										$themes[] = array('path'=>self::PUBLIC_PATH.$folder.'/','file'=>$file,'name'=>ucfirst($folder));
									}
								}
							}
						}
					}
				}
			}
		} else {
			return Jax_Response::Error('Unable to read themes directory.');
		}
		if ($internal == false){
			return Jax_Response::Valid($themes);
		} else {
			return $themes;
		}
	}
	
	/**
	 * Saves a selected theme for a user (if using authentication this is persisted server side)
	 * 
	 * @param string $theme
	 * @return mixed
	 */
	public static function saveTheme($theme)
	{
		if (self::_verifyTheme($theme))
		{
			if (Jax_Auth::verify()){
				$cache = Jax_Cache::getCache();
				$cacheId = 'UserTheme'.md5(Jax_Auth::getAuthId());
				$cache->save($theme,$cacheId,array(),null);
			}
		} else {
			return Jax_Response::Error('Unable to verify theme. The default theme will be restored after this session.');
		}
	}
	
	/**
	 * Retrieves a user theme for the current user. (if using authentication)
	 * 
	 * @return null;
	 */
	public static function getUserTheme()
	{
		$cache = Jax_Cache::getCache();
		$cacheId = 'UserTheme'.md5(Jax_Auth::getAuthId());
		
		if($cache->test($cacheId)){
			$userTheme = $cache->load($cacheId);
			$themes = self::getThemes(true);
			
			if (isset($themes['error'])) return $themes;
			
			foreach ($themes as $theme)
			{
				if (strtolower($theme['name']) == strtolower($userTheme)) return $theme;
			}
		}
		return null;
	}
	
	/**
	 * Verifies a theme is available
	 * 
	 * @param string $theme
	 * @return boolean
	 */
	private static function _verifyTheme($theme)
	{
		$publicDir = Jax_Config::getPublicPath();
		$themeDir = APPLICATION_PATH.'/../'.$publicDir.'/'.self::PUBLIC_PATH;
		if (is_dir($themeDir.$theme))
		{
			return true;
		} 
		return false;
	}
}