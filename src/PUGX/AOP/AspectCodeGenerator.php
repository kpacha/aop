<?php

namespace PUGX\AOP;

use CG\Core\ClassUtils;

/**
 * Encapsulates all the patterns for the enhaced proxy class generation
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class AspectCodeGenerator
{

    protected $prefix = '__CG_PUGX_AOP__';
    private $className;
    private $methodName;
    private $params;

    public function __construct($className, $methodName = null, $params = array(), $prefix = null)
    {
        if ($prefix) {
            $this->prefix = $prefix;
        }
        $this->className = $className;
        $this->methodName = $methodName;
        $this->params = $params;
    }

    /**
     * Get a simple return statement
     *
     * @return string
     */
    public function generateReturningCode()
    {
        return 'return $return;';
    }

    /**
     * Generate the code for an instantiation of a \ReflectionMethod object
     *
     * @return string
     */
    public function generateReflectionDeclarationCode()
    {
        return sprintf(
                        '$reflection = new \ReflectionMethod(%s, %s);',
                        var_export(ClassUtils::getUserClass($this->className), true),
                        var_export($this->methodName, true)
        );
    }

    /**
     * Generate the code for an invocation of a \ReflectionMethod
     * 
     * @return string
     */
    public function generateReflectionInvocationCode()
    {
        return sprintf('$return = $reflection->invokeArgs($this, array(%s));', $this->params);
    }

    /**
     * Generates the code for the aspect execution and the validation of its result
     *
     * @param string $aspectName
     * @param string $annotationClass
     * @param string $annotationParams
     * @return String
     */
    public function generateAspectCode($aspectName, $annotationClass, $annotationParams)
    {
        return sprintf(
                        'if(($result = $this->%s->trigger(new \%s(array(%s)), $this, \'%s\', array(%s))) !== null) return $result;',
                        $this->getAspectPropertyName($aspectName), $annotationClass, $annotationParams,
                        $this->methodName, $this->params
        );
    }

    /**
     * Generate the code for the aspect setting
     *
     * @param string $aspect
     * @return string
     */
    public function getSetterCode($aspect)
    {
        return '$this->' . $this->getAspectPropertyName($aspect) . ' = $aspect' . ucfirst($aspect) . ';';
    }

    /**
     * Generate the name for the aspect setter
     *
     * @param string $aspect
     * @return string
     */
    public function getSetterName($aspect)
    {
        return 'set' . $this->prefix . 'Aspect' . ucfirst($aspect);
    }

    /**
     * Generate the name of the property containing the received aspect
     *
     * @param string $aspect
     * @return string
     */
    public function getAspectPropertyName($aspect)
    {
        return $this->prefix . 'aspect' . $aspect;
    }

}

