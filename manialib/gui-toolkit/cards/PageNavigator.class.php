<?php
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 * @package ManiaLib
 */

/**
 * Page Navigator
 * Page navigation arrows at the bottom of the lists
 * @package ManiaLib
 * @subpackage GUIToolkit_Cards
 */
class PageNavigator extends GuiComponent
{
	/**
	 * @var Icons64x64_1
	 */
	public $arrowNext;
	/**
	 * @var Icons64x64_1
	 */
	public $arrowPrev;
	/**
	 * @var Icons64x64_1
	 */
	public $arrowFastNext;
	/**
	 * @var Icons64x64_1
	 */
	public $arrowFastPrev;
	/**
	 * @var Icons64x64_1
	 */
	public $arrowLast;
	/**
	 * @var Icons64x64_1
	 */
	public $arrowFirst;
	/**
	 * @var Label
	 */
	public $text;

	public $arrowNoneStyle = Icons64x64_1::StarGold;
	public $arrowNextStyle = Icons64x64_1::ArrowNext;
	public $arrowPrevStyle = Icons64x64_1::ArrowPrev;
	public $arrowFastNextStyle = Icons64x64_1::ArrowFastNext;
	public $arrowFastPrevStyle = Icons64x64_1::ArrowFastPrev;
	public $arrowFirstStyle = Icons64x64_1::ArrowFirst;
	public $arrowLastStyle = Icons64x64_1::ArrowLast;

	/**#@+
	 * @ignore
	 */
	protected $showLast;
	protected $showFastNext;
	protected $showText;
	protected $pageNumber;
	protected $currentPage;
	/**#@-*/

	function __construct($iconSize = 5)
	{
		$this->arrowNext = new Icon64($iconSize);
		$this->arrowPrev = new Icon64($iconSize);
		$this->arrowFastNext = new Icon64($iconSize);
		$this->arrowFastPrev = new Icon64($iconSize);
		$this->arrowLast = new Icon64($iconSize);
		$this->arrowFirst = new Icon64($iconSize);
		$this->text = new Label(5);
		
		$this->showLast = false;
		$this->showFastNext = false;
		$this->showText = true;
		
		$this->arrowNext->setSubStyle($this->arrowNoneStyle);
		$this->arrowPrev->setSubStyle($this->arrowNoneStyle);
		$this->arrowFastNext->setSubStyle($this->arrowNoneStyle);
		$this->arrowFastPrev->setSubStyle($this->arrowNoneStyle);
		$this->arrowLast->setSubStyle($this->arrowNoneStyle);
		$this->arrowFirst->setSubStyle($this->arrowNoneStyle);
	}
	
	/**
	 * Sets the size of the navigation icons
	 */
	function setSize($iconSize = 5, $nullValue=null)
	{
		$this->arrowNext->setSize($iconSize, $iconSize);
		$this->arrowPrev->setSize($iconSize, $iconSize);
		$this->arrowFastNext->setSize($iconSize, $iconSize);
		$this->arrowFastPrev->setSize($iconSize, $iconSize);
		$this->arrowLast->setSize($iconSize, $iconSize);
		$this->arrowFirst->setSize($iconSize, $iconSize);
	}
	
	/**
	 * Sets the page number
	 */
	function setPageNumber($pageNumber)
	{
		$this->pageNumber = $pageNumber;
	}
	
	/**
	 * Sets the current page
	 */
	function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
	}
	
	/**
	 * Shows or hides the "go to first/last" navigation icons
	 */
	function showLast($show = true)
	{
		$this->showLast = $show;
	}
	
	/**
	 * Returns whether the "go to first/last" navigation icons are shown 
	 */
	function isLastShown()
	{
		return $this->showLast;
	}
	
	/**
	 * Shows or hides the "fast prev/next" navigation icons
	 */
	function showFastNext($show = true)
	{
		$this->showFastNext = $show;
	}
	
	/**
	 * Returns whether the "fast prev/next" navigation icons are shown 
	 */
	function isFastNextShown()
	{
		return $this->showFastNext;
	}
	
	/**
	 * Shows or hides the text. Note that if the current page or the page number
	 * isn't declared, the text won't be shown
	 */
	function showText($show = true)
	{
		$this->showText = $show;
	}
	
	/**
	 * Returns whether the text is shown
	 */
	function isTextShown()
	{
		return $this->showText;
	}
	
	/**
	 * Saves the PageNavigator in the GUI objects stack
	 */
	function save()
	{
		// Show / hide text
		if(!$this->currentPage || !$this->pageNumber)
		{
			$this->showText(false);
		}
		
		// Auto show fast next / last
		if($this->arrowFirst->hasLink() || $this->arrowLast->hasLink() )
		{
			$this->showLast();
		}
		if($this->arrowFastNext->hasLink() || $this->arrowFastPrev->hasLink() )
		{
			$this->showFastNext();
		}
		
		// Arrow styles
		if($this->arrowNext->hasLink())
		{
			$this->arrowNext->setSubStyle($this->arrowNextStyle);
		} 
		if($this->arrowPrev->hasLink())
		{
			 $this->arrowPrev->setSubStyle($this->arrowPrevStyle);
		}
		if($this->arrowNext->hasLink() && $this->arrowFastNext->hasLink())
		{
			$this->arrowFastNext->setSubStyle($this->arrowFastNextStyle);
		}
		else
		{
			$this->arrowFastNext->setManialink(null);
		} 
		if($this->arrowPrev->hasLink() && $this->arrowFastPrev->hasLink())
		{
			 $this->arrowFastPrev->setSubStyle($this->arrowFastPrevStyle);
		}
		else
		{
			$this->arrowFastNext->setManialink(null);
		} 
		if($this->arrowNext->hasLink() && $this->arrowLast->hasLink())
		{
			 $this->arrowLast->setSubStyle($this->arrowLastStyle);
		}
		if($this->arrowPrev->hasLink() && $this->arrowFirst->hasLink())
		{
			 $this->arrowFirst->setSubStyle($this->arrowFirstStyle);
		}

		// Text
		$this->text->setStyle("TextStaticSmall");
		$this->text->setText($this->currentPage . "/" . $this->pageNumber);

		// Positioning in relation to the center of the containing frame
		$this->text->setAlign("center", "center");
		$this->text->setPositionZ(1);

		$this->arrowNext->setValign("center");
		$this->arrowFastNext->setValign("center");
		$this->arrowLast->setValign("center");

		$this->arrowNext->setPosition(($this->text->getSizeX() / 2) + 1, 0, 1);
		$this->arrowFastNext->setPosition($this->arrowNext->getPosX() + $this->arrowNext->getSizeX(), 0, 1);
		$this->arrowLast->setPosition(
			$this->arrowNext->getPosX() + 
			(int)$this->showFastNext*$this->arrowFastNext->getSizeX() + 
			$this->arrowNext->getSizeX(), 
			0, 1);

		$this->arrowPrev->setAlign("right", "center");
		$this->arrowFastPrev->setAlign("right", "center");
		$this->arrowFirst->setAlign("right", "center");

		$this->arrowPrev->setPosition(-($this->text->getSizeX()/2) - 1, 0, 1);
		$this->arrowFastPrev->setPosition($this->arrowPrev->getPosX() - $this->arrowPrev->getSizeX(), 0, 1);
		$this->arrowFirst->setPosition(
			$this->arrowPrev->getPosX() -
			(int)$this->showFastNext*$this->arrowFastPrev->getSizeX() - 
			$this->arrowPrev->getSizeX(),
			0, 1);

		// Save the gui
		Manialink::beginFrame($this->posX, $this->posY, $this->posZ);
		{
			if ($this->showText)
			{
				$this->text->save();
			}
			$this->arrowNext->save();
			$this->arrowPrev->save();
			if ($this->showLast)
			{
				$this->arrowFirst->save();
				$this->arrowLast->save();
			}
			if ($this->showFastNext)
			{
				$this->arrowFastNext->save();
				$this->arrowFastPrev->save();
			}
		}
		Manialink::endFrame();
	}
}

?>