<?php
/**
 * @group plugin_imagecarousel
 * @group plugins
 */
class plugin_imagecarousel_parse_test extends DokuWikiTest {

    public function setup() {
        $this->pluginsEnabled[] = 'imagecarousel';
        parent::setup();
    }

    public function test_syntax() {
        saveWikiText('test:plugin_imagecarousel', 
            '<carousel infinite=true&slidesToShow=4&slidesToScroll=3&dots=true>'
            .'  * {{:wiki:dokuwiki-128.png?direct|test}} Lorem Ipsum lorem ipsum'
            .'</carousel>',
            'setup for test');
        $HTML = p_wiki_xhtml('test:plugin_imagecarousel');
        $this->assertTrue(strpos($HTML, '<div class="slick') !== false, 'render class slick');
        $this->assertTrue(strpos($HTML, '"slidesToShow":4') !== false, 'slidesToShow is 4');
        
    }
}
