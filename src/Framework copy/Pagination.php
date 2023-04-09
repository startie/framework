<?php

namespace Startie;

class Pagination
{
	public $entriesPerPage;
	public $maxPages;
	public $align;
	public $EntitiesCount;
	public $controllerExpression;
	public $page;
	public $offset;
	public $per;
	public $totalEntities;
	public $totalPages;
	public $links;
	public $getParams;
	public $urlFirst;
	public $urlLast;
	public $urlNext;
	public $urlPrev;

	function __construct($entriesPerPage, $maxPages, $align, $EntitiesCount, $controllerExpression)
	{
		$this->entriesPerPage = $entriesPerPage;
		$this->maxPages = $maxPages;
		$this->align = $align;
		$this->EntitiesCount = $EntitiesCount;
		$this->controllerExpression = $controllerExpression;

		/* Get params */

		$getParams = [];
		foreach ($_GET as $key => $value) {
			if ($key != "url") {
				$getParams[$key] = $value;
			}
		}
		$this->getParams = $getParams;

		/* /Get params */

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

		/* Urls */

		$this->urlFirst = Url::c(
			$this->controllerExpression,
			null,
			array_merge(
				$this->getParams,
				[
					'page' => 1, 'per' => $this->per
				]
			)
		);

		$this->urlPrev = Url::c(
			$this->controllerExpression,
			null,
			array_merge(
				$this->getParams,
				[
					'page' => $this->page - 1, 'per' => $this->per
				]
			)
		);

		$this->urlNext = Url::c(
			$this->controllerExpression,
			null,
			array_merge(
				$this->getParams,
				[
					'page' => $this->page + 1, 'per' => $this->per
				]
			)
		);

		$this->urlLast = Url::c(
			$this->controllerExpression,
			null,
			array_merge(
				$this->getParams,
				[
					'page' => $this->totalPages, 'per' => $this->per
				]
			)
		);
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

	public function r()
	{
		return View::r('Pagination/Common', [
			'Pagination' => $this,
		]);
	}

	public function numbers()
	{
		if ($this->totalPages < $this->maxPages) {
			$this->maxPages = $this->totalPages;
		}

		# Counting bounderies
		if ($this->page == 1) {
			$previousPagesCount = 0;
			$nextPagesCount = $this->maxPages - 1;
		} else if ($this->page == 2) {
			$previousPagesCount = 1;
			$nextPagesCount = $this->maxPages - 2;
		} else if ($this->page < $this->totalPages - 1) {
			$previousPagesCount = ($this->maxPages - 1) / 2;
			$nextPagesCount = ($this->maxPages - 1) / 2;
		} else if ($this->page == $this->totalPages - 1) {
			$previousPagesCount = $this->maxPages - 2;
			$nextPagesCount = 1;
		} else if ($this->page == $this->totalPages) {
			$previousPagesCount = $this->maxPages - 1;
			$nextPagesCount = 0;
		}
		# /Counting bounderies

		$startPage = $this->page - $previousPagesCount;
		$endPage = $this->page + $nextPagesCount;

		# Generating links
		$this->links = [];
		for ($i = $startPage; $i <= $endPage; $i++) {
			if ($i == $this->page) {
				$activityClass = 'active';
			} else {
				$activityClass = '';
			};
			$this->links[] = ['activityClass' => $activityClass, 'pageNumber' => $i];
		}

		foreach ($this->links as &$link) {

			$link['url'] = Url::c(
				$this->controllerExpression,
				null,
				array_merge(
					$this->getParams,
					[
						'page' => $link['pageNumber'],
						'per' => $this->per
					]
				)
			);

			echo View::r('Pagination/Link', $link);
			echo " ";
		}
	}
}
