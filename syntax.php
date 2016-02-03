<?php
/**
 * DokuWiki Plugin imagecarousel (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  PeSt <peterstumm@gmx.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_imagecarousel extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'container';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 104;
    }
    
    public function getAllowedTypes() {
    	return array('substition');
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
//         $this->Lexer->addSpecialPattern('<carousel>.*?</carousel>',$mode,'plugin_imagecarousel');
    	$this->Lexer->addEntryPattern('<carousel.*?>',$mode,'plugin_imagecarousel');
//     	$this->Lexer->addSpecialPattern("\{\{[^\}]+\}\}",$mode,'media');
    }

   public function postConnect() {
   	   $this->Lexer->addPattern('\n {2,}[\*]','plugin_imagecarousel');
   	
       $this->Lexer->addExitPattern('</carousel>','plugin_imagecarousel');
   }
   
   protected $first_item = false;

    /**
     * Handle matches of the imagecarousel syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler){
        $data = array();
        $data['state'] = $state;
		switch($state) {
			case DOKU_LEXER_ENTER:
				$match = substr($match, 9,-1);  // strip markup
				$flags = array_merge(
						$this->getDefaultFlags(),
						$this->parseFlags($match)
				);
				$flags = json_encode($flags);
				$data['flags'] = $flags;
				break;
			case DOKU_LEXER_UNMATCHED:
				if (trim($match) !== '') {
					$handler->_addCall('cdata', array($match), $pos);
				}	
				break;
			case DOKU_LEXER_EXIT:
				$this->first_item = false;
				break;
			case DOKU_LEXER_MATCHED:
				if($match === "\n  *") {
					if(!$this->first_item) {
						$this->first_item = true;
						$data['first_item'] = true;
					}
				}
				break;
		}
        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {
        if($mode != 'xhtml') return false;
		
        if($data['state'] === DOKU_LEXER_ENTER) {
        	$renderer->doc .= '<div class="slick" data-slick=\''.$data['flags'].'\'>';
        } else if($data['state'] === DOKU_LEXER_EXIT) {
        	$renderer->doc .= '</div></div>';
        } else if($data['state'] === DOKU_LEXER_MATCHED) {
        	if(!isset($data['first_item'])) {
        		$renderer->doc .= '</div>';
        	}
        	$renderer->doc .= '<div>';
        }
        return true;
    }
    
    protected function getDefaultFlags() {
    	$conf = $this->getConf('default');
    	return $this->parseFlags($conf);
    }
    
    protected function parseFlags($confString) {
    	$confString = explode('&',$confString);
    	$flags = array();
    	foreach($confString as $flag) {
    		$tmp = explode('=',$flag,2);
    		if(count($tmp) === 2) {
    			if($tmp[1] === "true") $tmp[1] = true;
    			else if($tmp[1] === "false") $tmp[1] = false;
    			else if(is_numeric($tmp[1])) $tmp[1] = intval($tmp[1]);
    			$flags[$tmp[0]] = $tmp[1];
    		}
    	}
    	 
    	return $flags;
    }
}

// vim:ts=4:sw=4:et:
