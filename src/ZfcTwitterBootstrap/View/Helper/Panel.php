<?php
/**
 * ZfcTwitterBootstrap
 */

namespace ZfcTwitterBootstrap\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;

/**
 * Panel
 */
class Panel extends AbstractHelper
{
    /**
     * @var string
     */
    protected $format = <<<FORMAT
<div class="panel %s">
   <div class="panel-heading"><a data-toggle="collapse" data-target="#%s">%s</a></div>
    <div id="%s" class="panel-collapse collapse %s">
      <div class="panel-body">
        %s
      </div>
    </div>
</div>
FORMAT;

    /**
     * @param $header
     * @param $content
     * @param bool $collapsed
     *
     * @return string
     */
    public function info($header, $content, $collapsed = true)
    {
        return $this->render($header, $content, $collapsed, 'panel-info');
    }

    /**
     * @param $header
     * @param $content
     * @param bool $collapsed
     *
     * @return string
     */
    public function danger($header, $content, $collapsed = true)
    {
        return $this->render($header, $content, $collapsed, 'panel-danger');
    }

    /**
     * @param $header
     * @param $content
     * @param bool $collapsed
     *
     * @return string
     */
    public function success($header, $content, $collapsed = true)
    {
        return $this->render($header, $content, $collapsed, 'panel-success');
    }

    /**
     * @param $header
     * @param $content
     * @param bool $collapsed
     *
     * @return string
     */
    public function warning($header, $content, $collapsed = true)
    {
        return $this->render($header, $content, $collapsed, 'panel-warning');
    }

    /**
     * Render an Panel
     *
     * @param $header
     * @param $content
     * @param bool $collapsed
     * @param string $class
     *
     * @return string
     */
    public function render($header, $content, $collapsed = true, $class = '')
    {
        $class = trim($class);

        $id = rand(0, 100);

        return sprintf($this->format,
            $class,
            $id,
            $header,
            $id,
            (! $collapsed ?: 'in'),
            nl2br($content)
        );
    }

    /**
     * @param $header
     * @param $content
     * @param $collapsed
     *
     * @return $this|string
     */
    public function __invoke($header, $content, $collapsed)
    {
        if ($header) {
            return $this->render($header, $content, $collapsed, 'panel-default');
        }

        return $this;
    }
}
