<?php
    /**
     * @header Content-Type:text/xml; charset=utf-8
     * @Test Test
     */
    final class XML extends ThemeWrapper{
        function Render(IPage $page = null) {
            $markup = '<root/>';
            $this->_page = phpQuery::newDocumentXML($markup);
            $page->setPage($this->_page);
            $page->Render();
            return $this->_page;
        }
    }
