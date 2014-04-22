<?php
/**
 * ZfcTwitterBootstrap
 */

namespace ZfcTwitterBootstrap\View\Helper\Navigation;

use RecursiveIteratorIterator;

use Zend\View\Helper\Navigation\Menu as ZendMenu;
use Zend\Navigation\Navigation;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View;
use Zend\View\Exception;

/**
 * Helper for rendering menus from navigation containers
 */
class NosMenu extends ZendMenu
{
    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $ulClass = 'nav';


    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer $container          container to render
     * @param  string            $ulClass            CSS class for first UL
     * @param  string            $indent             initial indentation
     * @param  int|null          $minDepth           minimum depth
     * @param  int|null          $maxDepth           maximum depth
     * @param  bool              $onlyActive         render only active branch?
     * @param  bool              $escapeLabels       Whether or not to escape the labels
     * @param  bool              $addClassToListItem Whether or not page class applied to <li> element
     *
     * @return string
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem
    )
    {

        $html = '<ul class="' . $ulClass . '">';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);

        if ($found) {
            $foundPage = $found['page'];
        } else {
            return '';
        }


        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
            RecursiveIteratorIterator::SELF_FIRST);

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();

            if ($depth === 0) {
                continue;
            }

            $isActive = $page->isActive(true);

            if ($foundPage) {
                // page is not active itself, but might be in the active branch

                $accept = false;
                if ($foundPage->hasPage($page) ||
                    $foundPage->getParent()->hasPage($page) ||
                    (!$foundPage->getParent() instanceof Navigation &&
                        $foundPage->getParent()->getParent()->hasPage($page)
                    )

                ) {
                    // accept if page is a direct child of the active page
                    $accept = true;
                }


                if (!$accept) {
                    continue;
                }
            }


            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth === 1 && $page->hasChildren()) {
                // start new ul tag
                $page->isDropdown = true;
                $html .= $myIndent . '' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {

//                $page->setClass('btn btn-xs');
                // close previous li tag
                $html .= $myIndent . '   </li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($isActive) {
                $liClasses[] = 'active';
            }

            if ($depth === 2) {
                $liClasses[] = 'sub';
            }


            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . implode(' ', $liClasses) . '"';

            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL
                . $myIndent . '        ' . $this->htmlify($page, $escapeLabels, $addClassToListItem) . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }


        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 0; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i - 1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                    . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param  AbstractPage $page               page to generate HTML for
     * @param  bool         $escapeLabel        Whether or not to escape the label
     * @param  bool         $addClassToListItem Whether or not to add the page class to the list item
     *
     * @return string
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if (null !== ($translator = $this->getTranslator())) {
            $textDomain = $this->getTranslatorTextDomain();
            if (is_string($label) && !empty($label)) {
                $label = $translator->translate($label, $textDomain);
            }
            if (is_string($title) && !empty($title)) {
                $title = $translator->translate($title, $textDomain);
            }
        }

        // get attribs for element
        $element  = 'a';
        $extended = '';
        $attribs  = array(
            'id'    => $page->getId(),
            'title' => $title,
            'href'  => '#',
        );

        $class = array();
        if ($addClassToListItem === false) {
            $class[] = $page->getClass();
        }
        if ($page->isDropdown) {
//            $attribs['data-toggle'] = 'dropdown';
//            $class[]                = 'btn btn-default';
//            $extended = ' <span class="glyphicon glyphicon-play"></span>';
            $extended = ' &rsaquo;';
        }
        if (count($class) > 0) {
            $attribs['class'] = implode(' ', $class);
        }

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $attribs['href']   = $href;
            $attribs['target'] = $page->getTarget();
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>';
        if ($escapeLabel === true) {
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }

        $html .= $extended;
        $html .= '</' . $element . '>';

        return $html;
    }
}
