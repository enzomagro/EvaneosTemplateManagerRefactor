<?php

use EvaneosTemplateManager\src\Config\PatternToReplace;

/**
 * class Template Manager
 */
class TemplateManager
{
    /**
     * @var Template
     */
	private $_template;

	/**
     * @var array
     */
	private $_data;

	/**
     * Compute the template content
	 * @param Template $tpl
	 * @param array $data
	 * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
		try{
			$this->_template = $tpl;
			$this->_data = $data;
			$this->_template->subject = $this->_computeText($this->_template->subject);
			$this->_template->content = $this->_computeText($this->_template->content);
			return $this->_template;
		} catch (Exception $e){
			echo ' An error has occurs : <b>' . $e->getMessage() . '</b>';
		}
    }
	
	/**
     * Compute the content text of the template object
	 * @param string $text
	 * @return string
	 * @throws Exception
     */
	private function _computeText($text)
    {
		// Is a choice to require the Quote object to display the message 
        if (!isset($this->_data['quote']) || $this->_data['quote'] instanceof Quote === false){
			throw new \Exception ('quote place-holders are mandatory !');
        }
		$this->setContainsSummary($text);
		$this->setDestinationName($text);
		$this->setDestinationLink($text);
		$this->setUser($text);
		return $text;
    }

	/**
     * Compute the summary text of the template object
	 * @param string $text
	 * @return string
     */
	public function setContainsSummary(&$text)
	{
		$quote = $this->_data['quote'];
		$summary = [
			PatternToReplace::$patternToReplace['quote_summary'],
			PatternToReplace::$patternToReplace['quote_summary_html'],
		];
		$quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
		$text = str_replace(
			$summary,
			[$quote::renderText($quoteFromRepository), $quote::renderHtml($quoteFromRepository)],
			$text
		);

		return $text;
	}

	/**
     * Compute the destination link text of the template object
	 * @param string $text
	 * @return string
     */
	public function setDestinationLink(&$text)
	{
		$destination = DestinationRepository::getInstance()->getById($this->_data['quote']->destinationId);
		$site = SiteRepository::getInstance()->getById($this->_data['quote']->siteId);
		$quoteFromRepository = QuoteRepository::getInstance()->getById($this->_data['quote']->id);
		$text = str_replace(
			PatternToReplace::$patternToReplace['quote_destination_link'], 
			$site->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepository->id, 
			$text
		);

		return $text;
	}

	/**
     * Compute the destination name text of the template object
	 * @param string $text
	 * @return string
     */
	public function setDestinationName(&$text)
	{
		$destination = DestinationRepository::getInstance()->getById($this->_data['quote']->destinationId);
		$text = str_replace(
			PatternToReplace::$patternToReplace['quote_destination_name'],
			$destination->countryName,
			$text
		);

		return $text;
	}

	/**
     * Compute the user text of the template object
	 * @param string $text
	 * @return string
     */
	public function setUser(&$text)
	{
		if(isset($this->_data['user'])  and ($this->_data['user']  instanceof User)){
			$user = $this->_data['user'];
		} else {
			$user = ApplicationContext::getInstance()->getCurrentUser();
		}
		$text = str_replace(
			PatternToReplace::$patternToReplace['user_first_name'],
			ucfirst(mb_strtolower($user->firstname)),
			$text
		);

		return $text;
	}
}
