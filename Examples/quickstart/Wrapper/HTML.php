<?php
    /**
     * @Test Test
     */
    final class HTML extends ThemeWrapper{
        function Render(IPage $page = null) {
            $t = $this->_getTemplate($page->getWrapperTemplate());
            $this->_page = $t;
            $page->setPage($this->_page);
            $head = pq('head', $this->_page);
            $body = pq('body', $this->_page);
			
            $page->Render();
            /*
            //Speak manipulation
            foreach (pq('[data-speak]', $body) as $v) {
                $e = pq($v);
                $what = $e->attr('data-speak');
                $speak = $this->_root->getSite()->Speak($what);
                if($what != $speak)
                    if($e->is('input'))
                        $e->attr('value',$speak);
                    else
                        $e->html ($speak);
            }*/
            /*foreach (pq('[placeholder]', $body) as $v) {
                $e = pq($v);
                $what = $e->attr('placeholder');
                $speak = $this->_root->getSite()->Speak($what);
                if($what != $speak)
                    $e->attr('placeholder',$speak);
            }*/
            //Link manipulation
            foreach (pq('a', $body) as $v) {
                $e = pq($v);
                $href = $e->attr('href');
                var_dump($href);
                if ($href != '' && strpos($href, '?') === false && strpos($href, '/') === false && strpos($href, '#') === false) {
                    $e->attr('href', Map::generateUrl($href));
                }
            }
            
            if(!Site::isLoggedIn()){
            }
            return $this->_page;
        }
    }
