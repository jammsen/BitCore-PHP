<?php
    /**
     * @header Content-Type:text/plain
     * @Test Test
     */
    final class Ajax extends ThemeWrapper{
        function Render(IPage $page = null) {
            $t = '<html/>';
            $this->_page = phpQuery::newDocumentHTML($t);
            $page->setPage($this->_page);            
            $page->Render();
            return json_encode($page->_renderReturn);
        }
    }
