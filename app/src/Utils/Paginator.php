<?php

    namespace App\Utils;

    class Paginator {

        private $currentPage;
        private $totalItems;
        private $pageSize;
        private $queryString;
       
        public function __construct($queryString = '') {
            $this->currentPage = 0;
            $this->totalItems = 0;
            $this->pageSize = 5;
            $this->queryString = $queryString;
        }

        public function setCurrentPage(int $page) {
            # Trabalha com page - 1 para ajustar o offset do banco com o offset da pagina web
            $this->currentPage = intval($page - 1);
        }

        public function getCurrentPage() {
            return $this->currentPage + 1;
        }

        public function setTotalItems(int $total) {
            $this->totalItems = intval($total);
        }

        public function getTotalItems() {
            return $this->totalItems;
        }

        public function setPageSize(int $size) {
            $this->pageSize = intval($size);
        }

        public function getPageSize() {
            return $this->pageSize;
        }

        public function setQueryString($qs = null) {
            $this->queryString = $qs;
        }

        public function getQueryString() {
            return $this->queryString;
        }

        public function getOffset() {
            return intval($this->getCurrentPage() - 1) * $this->getPageSize();
        }

        public function totalPages() {
            return ceil( $this->totalItems / $this->pageSize );
        }

        public function getFirstPage() {
            return '?'. $this->queryString . '&page=1';
        }

        public function getLastPage() {
            $lastPage = 0;
            if ($this->totalItems != 0) {
                $lastPage = intval($this->totalPages());
            }
            return '?'. $this->queryString . '&page=' . $lastPage;
        }

        public function prev() {
            $prev = ( intval($this->getCurrentPage()) > 1 ) ? $this->getCurrentPage() - 1 : $this->getCurrentPage();
            return '?'. $this->queryString . '&page=' . $prev;
        }

        public function next() {
            $totalPages = $this->totalPages();

            if ($this->totalItems == 0) {
                return '?'. $this->queryString . '&page=' . $this->getCurrentPage();
            }

            $next = ( intval($this->getCurrentPage()) == $totalPages ) ? $this->getCurrentPage() : $this->getCurrentPage() + 1;
            return '?'. $this->queryString . '&page=' . $next;
        }

    }