<?php
/**
 * Bs3HtmlHelper file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @author        Codaxis (https://github.com/Codaxis/
 * @link          https://github.com/Codaxis/cakephp-bootstrap3-helpers
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('HtmlHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class Bs3HtmlHelper extends HtmlHelper {

/**
 * Default configuration.
 *
 * @var array
 */
	protected $_defaults = array(
		'iconVendorPrefixes' => array('fa', 'glyphicon'),
		'defaultIconVendorPrefix' => null,
	);

/**
 * Flag for active block rendering of components
 *
 * @var boolean
 */
	protected $_blockRendering = false;

/**
 * Current block rendering options
 *
 * @var array
 */
	protected $_blockOptions = array();

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$userConfig = Configure::check('Bs3.Html') ? Configure::read('Bs3.Html') : array();
		$this->_config = Hash::merge($this->_defaults, $userConfig);
	}


	public function cdnCss() {
		return $this->css('//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
	}


	public function cdnJs() {
		return $this->js('//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
	}

	public function cdnFontAwesome() {
		return $this->css('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css');
	}
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function icon($class, $options = array()) {
		$defaults = array();
		$options = array_merge($defaults, $options);

		$iconVendorPrefix = $this->getIconVendor($class);
		if ($iconVendorPrefix) {
			$class = $iconVendorPrefix . ' ' . $class;
		} else {
			$iconVendorPrefix = $this->_config['defaultIconVendorPrefix'];
			if ($iconVendorPrefix) {
				$class = $iconVendorPrefix . ' ' . $iconVendorPrefix . '-' . $class;
			}
		}

		$options['class'] = $class;
		return "\n".$this->tag('i', '', $options)."\n";
	}

/**
 * Returns icon vendor from class if found.
 */
	public function getIconVendor($class) {
		foreach ($this->_config['iconVendorPrefixes'] as $iconVendorPrefix) {
			$regex = sprintf('/^%s-|\s%s-/', $iconVendorPrefix, $iconVendorPrefix);
			if (preg_match($regex, $class)) {
				return $iconVendorPrefix;
			}
		}

		return null;
	}
        
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
        public function panelFooter($html, $options = array()) {
                $defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-footer');
		return $this->tag('div', $html, $options);
        }
        
/**
 * Render a panel heading
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function panelHeading($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-heading');
		return $this->tag('div', $html, $options);
	}

/**
 * Render a panel body
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function panelBody($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-body');
		return $this->tag('div', $html, $options);
	}

/**
 * Render a panel
 *
 * @param string $headingHtml
 * @param string $bodyHtml
 * @param string $footerHtml
 * @param array $options
 * @return string
 */
	public function panel($headingHtml, $bodyHtml = null, $footerHtml = null, $options = array()) {
		$defaults = array(
			'class' => 'panel-default', 'headingOptions' => array(), 'footerOptions' => array(), 'bodyOptions' => array(),
			'wrapHeading' => true, 'wrapFooter' => true, 'wrapBody' => true
		);
		if ($this->_blockRendering) {
			$options = $bodyHtml;
		}
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel');

		if (!$this->_blockRendering) {
			$heading = $options['wrapHeading'] ? $this->panelHeading($headingHtml, $options['headingOptions']) : $headingHtml;
                        $footer = $options['wrapFooter'] && $footerHtml ? $this->panelFooter($footerHtml, $options['footerOptions']) : $footerHtml;
			$body = $options['wrapBody'] ? $this->panelBody($bodyHtml, $options['bodyOptions']) : $bodyHtml;
			$html = $heading . $body . $footer;
		} else {
			$html = $headingHtml;
		}

		unset($options['headingOptions'], $options['footerOptions'], $options['bodyOptions'], $options['wrapFooter'], $options['wrapHeading'], $options['wrapBody']);
		return $this->tag('div', $html, $options);
	}

/**
 * Render an accordion
 *
 * @param mixed $items
 * @param array $options
 * @return string
 */
	public function accordion($items = array(), $options = array()) {
		$defaults = array(
			'class' => '', 'id' => str_replace('.', '', uniqid('accordion_', true)),
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel-group');

		if (is_array($items)) {
			$html = '';
			foreach ($items as $itemHeading => $itemBody) {
                                $itemOptions = empty($options[$itemHeading])?array():$options[$itemHeading];
                                $itemOptions['accordionId'] = $options['id'];
				$html .= $this->accordionItem($itemHeading, $itemBody, $itemOptions);
                                if(!empty($options[$itemHeading])){
                                    unset($options[$itemHeading]);
                                }
			}
		} else {
			$html= $items;
		}

		return $this->tag('div', $html, $options);
	}

/**
 * Render an accordion item
 *
 * @param mixed $titleHtml
 * @param mixed $bodyHtml
 * @param array $options
 * @return string
 */
	public function accordionItem($titleHtml, $bodyHtml = null, $options = array()) {
                $defaults = array(
			'headingOptions' => array(), 'bodyOptions' => array(),
                        'linkOptions' => array(), 'collapseOptions' => array(),
		);
                $options = Hash::merge($defaults, $options);
                
                $options['linkOptions'] = Hash::merge($options['linkOptions'], array(
			'data-toggle' => 'collapse', 'data-parent' => '#' . $options['accordionId']
		));
		$itemBodyId = str_replace('.', '', uniqid('accordion_body_', true));
		$titleLink = $this->link($titleHtml, '#' . $itemBodyId, $options['linkOptions']);
                unset($options['linkOptions']);

		$heading = $this->tag('h4', $titleLink, array('class' => 'panel-title'));
                
                $options['collapseOptions']['id'] = $itemBodyId;
                $options['collapseOptions'] = $this->addClass($options['collapseOptions'], 'panel-collapse collapse');
		$body = $this->tag('div', $this->panelBody($bodyHtml, $options['bodyOptions']), $options['collapseOptions']);
                unset($options['bodyOptions'], $options['collapseOptions']);
                
		$blockRendering = $this->_blockRendering;
		$this->_blockRendering = false;
                $options['wrapBody'] = false;
		$itemHtml = $this->panel($heading, $body, null, $options);
		$this->_blockRendering = $blockRendering;
		return $itemHtml;
	}
        
/**
 * Render a tab panel
 *
 * @param array $items Array of itens to be rendered
 * @param array $nav_items Array of tab names for navigation bar
 * @param int $active Index of default active tab
 * @param array $navOptions
 * @param array $options
 * @param boolean $pills Alternative pills tab
 * @param boolean $panel Render in a bootstrap panel or raw html
 * @return string
 */
	public function tab($items = array(), $nav_items = array(), $active = 0, $navOptions = array(), $options = array(), $pills = false, $panel = true ) {
                // uid
                $uid = uniqid('tab-');
                
                // tab-content
                $content_html = '';
                foreach ($items as $itemHeading => $itemBody) {
                    // Set nav default options if needed
                    if(empty($nav_items)){
                        $nav_items[$itemHeading] = $itemHeading;
                    }
                    
                    if(empty($navOptions[$itemHeading]['href'])) {
                        if (is_string($itemHeading)) {
                            $navOptions[$itemHeading] = array('href' => "#$itemHeading");
                        } else {
                            $navOptions[$itemHeading] = array('href' => "#$uid-$itemHeading");
                        }
                    }
                    
                    $itemOptions = empty($options[$itemHeading]) ? array() : $options[$itemHeading];
                    
                    if($active == $itemHeading) {
                        $itemOptions = $this->addClass($itemOptions, 'active');
                    }
                    
                    if(empty($itemOptions['id'])) {
                        if(is_string($itemHeading)) {
                            $itemOptions['id'] =  $itemHeading;
                        } else {
                            $itemOptions['id'] =  "$uid-$itemHeading";
                        }
                    }
                    
                    $content_html .= $this->tabItem($itemBody, $itemOptions);
                    if (!empty($options[$itemHeading])) {
                        unset($options[$itemHeading]);
                    }
                }
                
                // nav-tabs
                $nav_html = '';
                foreach ($nav_items as $navKey => $navBody) {
                    $nOptions = $navOptions[$navKey];
                    
                    if($active == $navKey) {
                        if(empty($nOptions['wrapperOptions'])) {
                            $nOptions['wrapperOptions'] = array();
                        }
                        $nOptions['wrapperOptions'] = $this->addClass($nOptions['wrapperOptions'], 'active');
                    }
                    
                    $nav_html .= $this->tabNav($navBody, $nOptions);
                    if (!empty($navOptions[$navKey])) {
                        unset($navOptions[$navKey]);
                    }
                }
                
                $nav_class = 'nav';
                $nav_style = '';
                if($pills) {
                    $nav_class .= ' nav-pills';
                } else {
                    $nav_class .= ' nav-tabs';
                }
                if($panel) $nav_style .= 'margin-bottom: -10px;border-bottom: none;';
                
                $nav_html = $this->tag('ul', $nav_html, array('class' => $nav_class, 'style' => $nav_style));
                
                if($panel) {
                    return $this->panel($nav_html, $this->tag('div', $content_html, array('class' => 'tab-content')));
                } else {
                    return $nav_html . "\n" . $this->tag('div', $content_html, array('class' => 'tab-content'));
                }
	}
        

/**
 * Render an tab panel item
 *
 * @param mixed $html
 * @param array $options
 * @return string
 */
	public function tabItem($html = null, $options = array()) {
                $defaults = array(
                    'class' => ''
                );
                $options = Hash::merge($defaults, $options);               
                $options = $this->addClass($options, 'tab-pane');
                
		$html = $this->tag('div', $html, $options);
                
		return $html;
	}
        
/**
 * Render an tab nav item
 *
 * @param mixed $html
 * @param array $options
 * @return string
 */
	public function tabNav($html = null, $options = array()) {
                $defaults = array(
                    'class' => '',
                    'data-toggle' => 'tab',
                    'href' => '#',
                    'wrapperOptions' => array()
                );
                $options = Hash::merge($defaults, $options);
                
                $wrapper_options = $options['wrapperOptions'];
                unset($options['wrapperOptions']);
                
                $html = $this->tag('a', $html, $options);
                
		$html = $this->tag('li', $html, $wrapper_options);
                
		return $html;
	}
        
	public function dropdown($toggle, $links = array(), $options = array()) {
		$defaults = array(
			'class' => '',
			'toggleClass' => 'btn btn-default',
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'dropdown');

		if ($this->_blockRendering) {
			$itemsHtml = $toggle;
			$toggle = $links;
		} else {
			if (is_array($links)) {
				$itemsHtml = '';
				foreach ($links as $item => $itemOptions) {
					$itemHtml = $before = $after = '';
					$liOptions = array();
					if (is_array($itemOptions)) {
						if ($this->_extractOption('active', $itemOptions)) {
							$liOptions['class'] = 'active';
						}

						if ($divider = $this->_extractOption('divider', $itemOptions)) {
							if ($divider === true) {
								$liOptions['class'] = 'divider';
							} else {
								${$divider} = $this->tag('li', '', array('class' => 'divider'));
							}
						}
						$itemHtml = $this->_extractOption('html', $itemOptions, '');
					} else {
						$itemHtml = $itemOptions;
					}

					$itemsHtml .= $before . $this->tag('li', $itemHtml, $liOptions) . $after;
				}
			} else {
				$itemsHtml= $links;
			}
		}

		$toggleOptions = array(
			'type' => 'button',
			'class' => $options['toggleClass'],
			'data-toggle' => 'dropdown'
		);
		$toggleOptions = $this->addClass($toggleOptions, 'sr-only dropdown-toggle');
		$toggleHtml = $this->tag('button', $toggle . ' <span class="caret"></span>', $toggleOptions);
		unset($options['toggleClass']);
		$itemsHtml = $this->tag('ul', $itemsHtml, array('class'=>'dropdown-menu'));

		$html = $toggleHtml . $itemsHtml;

		return $this->tag('div', $html, $options);
	}
        
        /**
         * Render a modal block
         * @param string $title
         * @param string $content
         * @param array $options
         * @return string Modal HTML code
         */
        public function modal($headingHtml, $bodyHtml, $footerHtml = null, $options = array()) {
            $defaults = array(
                'class' => 'fade',
                'tabindex' => '-1',
                'role' => 'dialog',
                'aria-hidden' => true,
                'dialogOptions' => array(),
                'headingOptions' => array(),
                'bodyOptions' => array(),
                'footerOptions' => array()
            );
            
            $options = Hash::merge($defaults, $options);
            $options = $this->addClass($options, 'modal');
            
            // Define ID
            if(empty($options['id'])) $options['id'] = str_replace('.', '', uniqid('modal-', true));
            $options['aria-labelledby'] = $options['id'].'-label';
            
            if (!$this->_blockRendering) {
                $modalContent = $this->modalHeading($headingHtml, $options['headingOptions']);
                $modalContent .= $this->modalBody($bodyHtml, $options['bodyOptions']);
                $modalContent .= $footerHtml?$this->modalFooter($footerHtml, $options['footerOptions']):$footerHtml;
            } else {
                $modalContent = $this->modalHeading($headingHtml, $options['headingOptions']);
            }
            
            $modalContent = $this->tag('div', $modalContent, array('class' => 'modal-content'));
            $modalDialog = $this->modalDialog($modalContent, $options['dialogOptions']);
            unset($options['headingOptions'], $options['footerOptions'], $options['bodyOptions'], $options['dialogOptions']);
            $modal = $this->tag('div', $modalDialog, $options);            
            return $modal;
        }
        
        public function modalHeading($html, $options = array()) {
            $defaults = array('class' => '');
            $options = array_merge($defaults, $options);
            $options = $this->addClass($options, 'modal-header');
            return $this->tag('div', $html, $options);
        }
        
        public function modalBody($html, $options = array()) {
            $defaults = array('class' => '');
            $options = array_merge($defaults, $options);
            $options = $this->addClass($options, 'modal-body');
            return $this->tag('div', $html, $options);
        }
        
        public function modalFooter($html, $options = array()) {
            $defaults = array('class' => '');
            $options = array_merge($defaults, $options);
            $options = $this->addClass($options, 'modal-footer');
            return $this->tag('div', $html, $options);
        }
        
        public function modalDialog ($html, $options = array()) {
            $defaults = array('class' => '');
            $options = array_merge($defaults, $options);
            $options = $this->addClass($options, 'modal-dialog');
            return $this->tag('div', $html, $options);
        }
        
        /**
         * Render a complete dialog modal with title and buttons
         * @param string $title Dialog Title
         * @param string $content Dialog Rendered Content
         * @param array $options Settings
         */
        public function dialog($title = null, $content, $options = array()) {
            $defaults = array('class' => 'fade dialog');
            
            $options = Hash::merge($defaults, $options);
            
            // Define ID
            if(empty($options['id'])) $options['id'] = str_replace('.', '', uniqid('modal-', true));
            
            // TODO: Traduzir sentença abaixo
            if(empty($title)) $title = __('Diálogo');
            
            $headingHtml = $this->useTag('button', array(
                'class' => 'close',
                'data-dismiss' => 'modal',
                'aria-hidden' => 'true'
            ), 'x');
            $headingHtml .= $this->tag('h4', $title, array(
                'class' => 'modal-title',
                'id' => "{$options['id']}-label"
            ));
            
            // Renderizar botões
            $footerHtml = '';
            if(!empty($options['buttons'])) {
                foreach($options['buttons'] as $bOptions) {
                    $footerHtml .= $this->useTag('button', $bOptions, $bOptions['value']);
                }
                unset($options['buttons']);
            }
            // TODO: Traduzir sentença abaixo
            if(empty($options['closeButton'])) {
                $footerHtml .= $this->useTag('button', array(
                    'class' => 'btn btn-default',
                    'data-dismiss' => 'modal'
                ), __('Fechar'));
            } else if($options['closeButton'] != FALSE) {
                $options['closeButton']['data-dismiss'] = 'modal';
                $footerHtml .= $this->useTag('button', $options['closeButton'], $options['closeButton']['value']);
            }
                      
            if(empty($options['launchButton']) && $options['launchButton']!==FALSE) {
                    // TODO: Traduzir sentença abaixo
                    $bTitle = __('Abrir Diálogo');
                    $launchButton = $this->dialogButton($bTitle, $options['id']);
                    unset($options['launchButton']);
                    $dialog = $launchButton.$this->modal($headingHtml, $content, $footerHtml, $options);
            } else {
                    if($options['launchButton']===FALSE) {
                        $dialog = $this->modal($headingHtml, $content, $footerHtml, $options);
                        unset($options['launchButton']);
                    } else {
                        $bTitle = $options['launchButton']['value'];
                        unset($options['launchButton']['value']);
                        $bOptions = $options['launchButton'];
                        $launchButton = $this->dialogButton($bTitle, $options['id'], $bOptions);
                        unset($options['launchButton']);
                        $dialog = $launchButton.$this->modal($headingHtml, $content, $footerHtml, $options);
                    }
            }
            return $dialog;
        }
        
        public function dialogButton($title, $dataTarget, $options = array()) {
            $defaults = array('class' => 'btn btn-default');
            
            $options = Hash::merge($defaults, $options);
            $options['data-toggle'] = 'modal';
            $options['data-target'] = "#{$dataTarget}";
            
            return $this->useTag('button', $options, $title);
        }
        

/**
 * Handles custom method calls, like findBy<field> for DB models,
 * and custom RPC calls for remote data sources.
 *
 * @param string $method Name of method to call.
 * @param array $params Parameters for the method.
 * @return mixed Whatever is returned by called method
 */
	public function __call($method, $params) {
		if (substr($method, -5) == 'Start') {
			$call = substr($method, 0, strlen($method) - 5);
			if (method_exists($this, $call)) {
				$this->_View->assign($call . '_block', null);
				$this->_blockOptions[$call . '_block_options'] = isset($params[0]) ? $params[0] : array();
				$this->_View->start($call . '_block');
				$this->_blockRendering = true;
			}
		} elseif (substr($method, -3) == 'End') {
			$call = substr($method, 0, strlen($method) - 3);
			if (method_exists($this, $call)) {
				$this->_View->end($call . '_block');
				$html = $this->_View->fetch($call . '_block');
				$generatedHtml = $this->$call($html, $this->_blockOptions[$call . '_block_options']);
				$this->_blockRendering = false;
				return $generatedHtml;
			}
		}
	}

/**
 * Extracts a single option from an options array.
 *
 * @param string $name The name of the option to pull out.
 * @param array $options The array of options you want to extract.
 * @param mixed $default The default option value
 * @return mixed the contents of the option or default
 */
	protected function _extractOption($name, $options, $default = null) {
		if (array_key_exists($name, $options)) {
			return $options[$name];
		}
		return $default;
	}
}
