<?php
    /**
     * @header Content-Type:text/plain
     * @Test Test
     */
    final class Plain extends ThemeWrapper{
        function Render(IPage $page = null) {
            $t = '<html/>';
            $this->_page = phpQuery::newDocumentHTML($t);
            $page->setPage($this->_page);            
            $page->Render();
            return $page->_renderReturn;
        }
    }
