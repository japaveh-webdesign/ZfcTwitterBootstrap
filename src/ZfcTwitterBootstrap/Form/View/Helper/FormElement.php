<?php
/**
 * ZfcTwitterBootstrap
 */

namespace ZfcTwitterBootstrap\Form\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Helper\EscapeHtml;

/**
 * Form Element
 */
class FormElement extends ZendFormElement
{
    /**
     * @var \Zend\Form\View\Helper\FormLabel
     */
    protected $labelHelper;

    /**
     * @var \Zend\Form\View\Helper\ZendFormElement
     */
    protected $elementHelper;

    /**
     * @var \Zend\View\Helper\EscapeHtml
     */
    protected $escapeHelper;

    /**
     * @var \Zend\Form\View\Helper\FormElementErrors
     */
    protected $elementErrorHelper;

    /**
     * @var FormDescription
     */
    protected $descriptionHelper;
    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $groupWrapper = '<div class="form-group%s" id="control-group-%s">%s</div>';

    /**
     * @var string
     */
    protected $controlWrapper = '<div class="col-lg-9">%s%s%s</div>';

    /**
     * Set Label Helper
     *
     * @param  \Zend\Form\View\Helper\FormLabel $labelHelper
     *
     * @return self
     */
    public function setLabelHelper(FormLabel $labelHelper)
    {
        $labelHelper->setView($this->getView());
        $this->labelHelper = $labelHelper;

        return $this;
    }

    /**
     * Get Label Helper
     *
     * @return \Zend\Form\View\Helper\FormLabel
     */
    public function getLabelHelper()
    {
        if ( ! $this->labelHelper) {
            $this->setLabelHelper($this->view->plugin('formlabel'));
        }

        return $this->labelHelper;
    }

    /**
     * Set EscapeHtml Helper
     *
     * @param  \Zend\View\Helper\EscapeHtml $escapeHelper
     *
     * @return self
     */
    public function setEscapeHtmlHelper(EscapeHtml $escapeHelper)
    {
        $escapeHelper->setView($this->getView());
        $this->escapeHelper = $escapeHelper;

        return $this;
    }

    /**
     * Get EscapeHtml Helper
     *
     * @return \Zend\View\Helper\EscapeHtml
     */
    public function getEscapeHtmlHelper()
    {
        if ( ! $this->escapeHelper) {
            $this->setEscapeHtmlHelper($this->view->plugin('escapehtml'));
        }

        return $this->escapeHelper;
    }

    /**
     * Set Element Helper
     *
     * @param  \Zend\Form\View\Helper\FormElement $elementHelper
     *
     * @return self
     */
    public function setElementHelper(ZendFormElement $elementHelper)
    {
        $elementHelper->setView($this->getView());
        $this->elementHelper = $elementHelper;

        return $this;
    }

    /**
     * Get Element Helper
     *
     * @return \Zend\Form\View\Helper\FormElement
     */
    public function getElementHelper()
    {
        if ( ! $this->elementHelper) {
            $this->setElementHelper($this->view->plugin('formelement'));
        }

        return $this->elementHelper;
    }

    /**
     * Set Element Error Helper
     *
     * @param  \Zend\Form\View\Helper\FormElementErrors $errorHelper
     *
     * @return self
     */
    public function setElementErrorHelper(FormElementErrors $errorHelper)
    {
        $errorHelper->setView($this->getView());

        $this->elementErrorHelper = $errorHelper;

        return $this;
    }

    /**
     * Get Element Error Helper
     *
     * @return \Zend\Form\View\Helper\FormElementErrors
     */
    public function getElementErrorHelper()
    {
        if ( ! $this->elementErrorHelper) {
            $this->setElementErrorHelper($this->view->plugin('formelementerrors'));
        }

        return $this->elementErrorHelper;
    }

    /**
     * Set Description Helper
     *
     * @param FormDescription
     *
     * @return self
     */
    public function setDescriptionHelper($descriptionHelper)
    {
        $descriptionHelper->setView($this->getView());
        $this->descriptionHelper = $descriptionHelper;

        return $this;
    }

    /**
     * Get Description Helper
     *
     * @return FormDescription
     */
    public function getDescriptionHelper()
    {
        if ( ! $this->descriptionHelper) {
            $this->setDescriptionHelper($this->view->plugin('ztbformdescription'));
        }

        return $this->descriptionHelper;
    }

    /**
     * Set Group Wrapper
     *
     * @param  string $groupWrapper
     *
     * @return self
     */
    public function setGroupWrapper($groupWrapper)
    {
        $this->groupWrapper = (string)$groupWrapper;

        return $this;
    }

    /**
     * Get Group Wrapper
     *
     * @return string
     */
    public function getGroupWrapper()
    {
        return $this->groupWrapper;
    }

    /**
     * Set Control Wrapper
     *
     * @param  string $controlWrapper ;
     *
     * @return self
     */
    public function setControlWrapper($controlWrapper)
    {
        $this->controlWrapper = (string)$controlWrapper;

        return $this;
    }

    /**
     * Get Control Wrapper
     *
     * @return string
     */
    public function getControlWrapper()
    {
        return $this->controlWrapper;
    }

    /**
     * Render
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     *
     * @return string
     */
    public function render(ElementInterface $element, $groupWrapper = null, $controlWrapper = null)
    {
        $labelHelper        = $this->getLabelHelper();
        $escapeHelper       = $this->getEscapeHtmlHelper();
        $elementHelper      = $this->getElementHelper();
        $elementErrorHelper = $this->getElementErrorHelper();
        $descriptionHelper  = $this->getDescriptionHelper();
        $groupWrapper       = $groupWrapper ?: $this->groupWrapper;
        $controlWrapper     = $controlWrapper ?: $this->controlWrapper;
        $renderer           = $elementHelper->getView();

        $hiddenElementForCheckbox = '';
        if (method_exists($element, 'useHiddenElement') && $element->useHiddenElement()) {
            // If we have hidden input with checkbox's unchecked value, render that separately so it can be prepended later, and unset it in the element
            $withHidden               = $elementHelper->render($element);
            $withoutHidden            = $elementHelper->render($element->setUseHiddenElement(false));
            $hiddenElementForCheckbox = str_ireplace($withoutHidden, '', $withHidden);
        }

        $id = $element->getAttribute('id') ?: $element->getAttribute('name');

        /**
         * Dedicated control-wrapper for inline forms
         */
        if ($element->getOption('inline')) {
            $controlWrapper = '%s%s%s';
        }

        switch (true) {
            case $element instanceof \Zend\Form\Element\Radio:
            case $element instanceof \Zend\Form\Element\Checkbox:
                if ($element->getOption('wrapCheckboxInLabel')) {
                    $controlWrapper = '<div class="checkbox">%s%s%s</div>';
                }
                break;
            case $element instanceof \Zend\Form\Element\Hidden:
            case $element instanceof \Zend\Form\Element\Button:
            case $element instanceof \Zend\Form\Element\Submit:
                break;
            default:
                $element->setAttribute('class', 'form-control');
                break;
        }


        $controlLabel = '';
        $label        = $element->getLabel();
        if (strlen($label) === 0) {
            $label = $element->getOption('label') ?: $element->getAttribute('label');
        }

        if ($label && ! $element->getOption('skipLabel')) {

            $controlLabel .= $labelHelper->openTag(
                [
                    'class' => (! $element->getOption('inline') ? 'col-lg-3 ' : '') .
                        ($element->getOption('wrapCheckboxInLabel') ? '' : 'control-label'),
                ] + ($element->hasAttribute('id') ? ['for' => $id] : [])
            );

            if (null !== ($translator = $labelHelper->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $labelHelper->getTranslatorTextDomain()
                );
            }
            if ($element->getOption('wrapCheckboxInLabel')) {
                $controlLabel .= $elementHelper->render($element) . ' ';
            }
            if ($element->getOption('skipLabelEscape')) {
                $controlLabel .= $label;
            } else {
                $controlLabel .= $escapeHelper($label);
            }
            $controlLabel .= $labelHelper->closeTag();
        }

        if ($element->getOption('wrapCheckboxInLabel')) {
            $controls     = $controlLabel;
            $controlLabel = '';
        } else {
            $controls = $elementHelper->render($element);
        }

        if ($element instanceof \Zend\Form\Element\Radio) {
            $controls = str_replace(
                ['<label', '</label>'],
                ['<div class="radio"><label', '</label></div>'],
                $controls
            );
        } elseif ($element instanceof \Zend\Form\Element\MultiCheckbox) {
            $controls = str_replace(
                ['<label', '</label>'],
                ['<div class="checkbox"><label', '</label></div>'],
                $controls
            );
        }


        if ($element instanceof \Zend\Form\Element\Hidden || $element instanceof \Zend\Form\Element\Submit
            || $element instanceof \Zend\Form\Element\Button
        ) {
            return $controls . $elementErrorHelper->render($element);
        } else {
            $html = $hiddenElementForCheckbox . $controlLabel . sprintf(
                    $controlWrapper,
                    $controls,
                    $descriptionHelper->render($element),
                    $elementErrorHelper->render($element)
                );
        }


        $addtClass = ($element->getMessages()) ? ' has-error' : '';

        if ($element->hasAttribute('required')) {
            $addtClass .= $addtClass . ' required';
        }

        return sprintf($groupWrapper, $addtClass, $id, $html);
    }

    /**
     * Magical Invoke
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     *
     * @return string|self
     */
    public function __invoke(ElementInterface $element = null, $groupWrapper = null, $controlWrapper = null)
    {
        if ($element) {
            return $this->render($element, $groupWrapper, $controlWrapper);
        }

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ContainerInterface $serviceLocator
     *
     * @return FormElement
     */
    public function setServiceLocator(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}
