<?php
/**
 * @package axy\phpcode\compile
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\phpcode\compile;

/**
 * Generator for lambda functions
 */
class Lambda
{
    /**
     * The constructor
     *
     * @param string|string[] $code
     *        the code or code lines list
     * @param string[] $args [optional]
     *        the names list
     * @param string[] $used
     *        the used classes list
     */
    public function __construct($code, array $args = null, array $used = null)
    {
        if (!is_array($code)) {
            $code = [$code];
        }
        $this->code = $code;
        $this->args = $args ?: [];
        $this->used = $used ?: [];
    }

    /**
     * Appends a line to the code
     *
     * @param string $line
     */
    public function appendCodeLine($line)
    {
        $this->callback = null;
        $this->code[] = $line;
    }

    /**
     * Returns the code (inside of the function)
     *
     * @return string
     */
    public function getInnerCode()
    {
        return implode(PHP_EOL, $this->code);
    }

    /**
     * Returns the arguments list
     *
     * @return string
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Returns the used classes list
     *
     * @return string
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Returns the function code
     *
     * @return string
     */
    public function getOuterCode()
    {
        $parts = [];
        foreach ($this->used as $used) {
            $parts[] = 'use '.$used.';';
        }
        $args = [];
        foreach ($this->args as $arg) {
            $args[] = '$'.$arg;
        }
        $parts[] = 'return function ('.implode(',', $args).') {';
        $parts[] = $this->getInnerCode();
        $parts[] = '};';
        return implode(PHP_EOL, $parts);
    }

    /**
     * Returns the callback
     *
     * @return callable
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function getCallback()
    {
        if (!$this->callback) {
            $this->callback = eval($this->getOuterCode());
        }
        return $this->callback;
    }

    /**
     * Calls the lambda
     *
     * @param array $args [optional]
     * @return mixed
     */
    public function call(array $args = null)
    {
        return call_user_func_array($this->getCallback(), $args ?: []);
    }

    /**
     * Magic invoke
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->call(func_get_args());
    }

    /**
     * Returns the content of PHP-file which returns a callback
     *
     * @param $comment [optional]
     * @return string
     */
    public function getContentForFile($comment = null)
    {
        $content = ['<?php'];
        if ($comment) {
            $content[] = '/* '.$comment.' */';
        }
        $content[] = $this->getOuterCode();
        return implode(PHP_EOL, $content).PHP_EOL;
    }

    /**
     * Saves the code to a file
     *
     * @param string $filename
     * @param string $comment [optional]
     */
    public function save($filename, $comment = null)
    {
        file_put_contents($filename, $this->getContentForFile($comment));
    }

    /**
     * @var string[]
     */
    private $code;

    /**
     * @var string[]
     */
    private $args;

    /**
     * @var string[]
     */
    private $used;

    /**
     * @var callable
     */
    private $callback;
}
