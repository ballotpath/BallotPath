<?php

// require_once 'utils/string_utils.php';
include_once dirname(__FILE__) . '/' . 'utils/string_utils.php';

class InputMethod
{
    const Post = 1;
    const Get = 2;
    const Session = 3;
}

/**
 * Wraps the superglobal variables such as $_POST, $_GET, $_SESSION
 */
class SuperGlobals
{
    private $context;

    public function __construct($context = null)
    {
        $this->context = $context;
    }

    // TODO make a decorator
    private function RemoveContextFromName($name)
    {
        return StringUtils::IsNullOrEmpty($this->context) ? 
            $name :
            StringUtils::Replace($this->context . '_', '', $name);
    }

    private function IsNameInContext($name)
    {
        return StringUtils::IsNullOrEmpty($this->context) ? 
            true : 
            StringUtils::StartsWith($name, $this->context . '_');
    }

    private function GetNameInContext($name)
    {
        return StringUtils::IsNullOrEmpty($this->context) ? $name : ($this->context . '_' . $name);
    }
    
    private function GetArrayByInputMethod($method)
    {
        switch ($method)
        {
            case InputMethod::Get:
                return $_GET;
                break;
            case InputMethod::Post:
                return $_POST;
                break;
            case InputMethod::Session:
                return $_SESSION;
                break;
            default:
                throw new Exception('Unknown InputMethod value');
                break;
        }
    }

    public function RefineInputValue($value)
    {
        if(get_magic_quotes_gpc())
        {
            if (is_array($value))
                return $value;
            else
                return stripslashes($value);
        }
        return $value;
    }

    public function IsInputValueSet($name, $method)
    {
        $inputArray = $this->GetArrayByInputMethod($method);
        return isset($inputArray[$this->GetNameInContext($name)]);
    }

    public function GetInputValue($name, $method)
    {
        $inputArray = $this->GetArrayByInputMethod($method);
        return $this->RefineInputValue($inputArray[$this->GetNameInContext($name)]);
    }

    public function SetInputValue($name, $value, $method)
    {
        $inputArray = $this->GetArrayByInputMethod($method);
        $inputArray[$this->GetNameInContext($name)] = $value;
    }

    public function UnSetInputValue($name, $method)
    {
        $inputArray = $this->GetArrayByInputMethod($method);
        unset($inputArray[$this->GetNameInContext($name)]);
    }

    public function IsPostValueSet($name)
    {
        return $this->IsInputValueSet($name, InputMethod::Post);
    }

    public function IsGetValueSet($name)
    {
        return $this->IsInputValueSet($name, InputMethod::Get);
    }

    public function GetPostValueDef($name, $defaultValue = null)
    {
        return $this->IsPostValueSet($name) ? $this->GetPostValue($name) : $defaultValue;
    }

    public function GetGetValueDef($name, $defaultValue = null)
    {
        return $this->IsGetValueSet($name) ? $this->GetGetValue($name) : $defaultValue;
    }

    public function GetPostValue($name)
    {
        return $this->GetInputValue($name, InputMethod::Post);
    }

    public function GetGetValue($name)
    {
        return $this->GetInputValue($name, InputMethod::Get);
    }

    public function GetInputVariablesIf($predicate, $inputMethod)
    {
        $inputArray = $this->GetArrayByInputMethod($inputMethod);
        $result = array();
        foreach($inputArray as $name => $value)
            if ($this->IsNameInContext($name) && $predicate($this->RemoveContextFromName($name)))
                $result[$this->RemoveContextFromName($name)] = $value;
        return $result;
    }    

    public function GetPostVariablesIf($predicate)
    {
        return $this->GetInputVariablesIf($predicate, InputMethod::Post);
    }

    #region Session routines

    public function IsSessionVariableSet($name)
    {
        return isset($_SESSION[$this->GetNameInContext($name)]);
    }

    public function GetSessionVariable($name)
    {
        return $_SESSION[$this->GetNameInContext($name)];
    }

    public function SetSessionVariable($name, $value)
    {
        $_SESSION[$this->GetNameInContext($name)] = $value;
    }

    public function UnSetSessionVariable($name)
    {
        unset($_SESSION[$this->GetNameInContext($name)]);
    }

    #endregion
}
