<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if(strpos($text, '[quote:destination_link]') !== false){
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
        }

        if (isset($destination))
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        else
            $text = str_replace('[quote:destination_link]', '', $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
        }

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
