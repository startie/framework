<?php

namespace Startie;

class Pagination
{
	function __construct($entriesPerPage, $maxPages, $align, $EntitiesCount)
	{
		$this->entriesPerPage = $entriesPerPage;
		$this->maxPages = $maxPages;
		$this->align = $align;
		$this->EntitiesCount = $EntitiesCount;

		if (Input::is('GET', 'page')) {
			$this->page = Input::get('page', 'INT');
		} else {
			$this->page = 1;
		}

		if (Input::is('GET', 'per')) {
			$this->per = Input::get('per', 'INT');
		} else {
			$this->per = $this->entriesPerPage;
		}

		if ($this->page == 1) {
			$this->offset = 0;
		} else {
			$this->offset = $this->page * $this->per - $this->per;
		}
		$this->totalEntities = $EntitiesCount;
		$this->totalPages = ceil($this->totalEntities / $this->per);
	}

	public function cut($EntitiesArr)
	{
		return array_slice($EntitiesArr, $this->offset, $this->per);
	}

	public function offset()
	{
		return $this->offset;
	}

	public function limit()
	{
		return $this->per;
	}

	public function ify($params)
	{
		$params['offset'] = $this->offset();
		$params['limit'] = $this->limit();
		return $params;
	}
}
