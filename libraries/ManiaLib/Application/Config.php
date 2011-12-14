<?php
/**
 * ManiaLib - Lightweight PHP framework for Manialinks
 *
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaLib\Application;

/**
 * Application config
 * @method \ManiaLib\Application\Config getInstance()
 */
class Config extends \ManiaLib\Utils\Singleton
{

	public $URL;
	public $manialink;
	public $namespace;
	public $langsURL;
	public $imagesURL;
	public $mediaURL;
	public $useRewriteRules = false;
	public $defaultController = 'Home';
	public $defaultAction = 'index';
	public $viewsNS = array('ManiaLib\\Application\\Views\\');
	public $renderer;
	public $webapp = false;
	public $debug = false;

	function getMediaURL()
	{
		return $this->mediaURL? : $this->URL.'media/';
	}
	
	function getLangsURL()
	{
		return $this->langsURL? : $this->getMediaURL().'langs/';
	}

	function getImagesURL()
	{
		return $this->imagesURL? : $this->getMediaURL().'images/';
	}

	function getLinkCreationURL()
	{
		return $this->useRewriteRules ?
			substr($this->URL, 0, -1) : $this->URL.'index.php';
	}

	function getViewsNS()
	{
		return $this->viewsNS;
	}

	function getRenderer()
	{
		if($this->renderer)
		{
			return $this->renderer;
		}
		elseif($this->webapp)
		{
			return 'ManiaLib\\Application\\Rendering\\SimpleTemplates';
		}
		else
		{
			return 'ManiaLib\\Application\\Rendering\\Manialink';
		}
	}

}

?>