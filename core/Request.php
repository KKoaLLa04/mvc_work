<?php

class Request
{
    private $__rules = [], $__messages = [], $__errors = [];

    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isPost()
    {
        if ($this->getMethod() == 'POST') {
            return true;
        }
        return false;
    }

    public function isGet()
    {
        if ($this->getMethod() == 'GET') {
            return true;
        }

        return false;
    }

    public function getFields()
    {
        $dataFields = [];

        if ($this->isGet()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    if (is_array($value)) {
                        $dataFields[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $dataFields[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }

        if ($this->isPost()) {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    if (is_array($value)) {
                        $dataFields[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $dataFields[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }

        return $dataFields;
    }

    public function rules($rules = [])
    {
        $this->__rules = $rules;
    }

    public function messages($messages = [])
    {
        $this->__messages = $messages;
    }

    public function validate()
    {
        $this->__rules = array_filter($this->__rules);

        $checkValidate = true;
        if (!empty($this->__rules)) {

            $dataField = $this->getFields();

            foreach ($this->__rules as $fieldName => $ruleItem) {

                $ruleItemArr = explode('|', $ruleItem);

                if (!empty($ruleItemArr)) {
                    foreach ($ruleItemArr as $rules) {
                        $ruleName = null;
                        $ruleValue = null;

                        $ruleArr = explode(':', $rules);

                        $ruleName = reset($ruleArr);
                        if (!empty($ruleArr) && count($ruleArr) > 1) {
                            $ruleValue = end($ruleArr);
                        }


                        if ($ruleName == 'required') {
                            if (empty(trim($dataField[$fieldName]))) {
                                $this->setErrors($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }

                        if ($ruleName == 'min') {
                            if (strlen(trim($dataField[$fieldName])) < $ruleValue) {
                                $this->setErrors($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }

                        if ($ruleName == 'max') {
                            if (strlen(trim($dataField[$fieldName])) > $ruleValue) {
                                $this->setErrors($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }

                        if ($ruleName == 'email') {
                            if (!filter_var(trim($dataField[$fieldName]), FILTER_VALIDATE_EMAIL)) {
                                $this->setErrors($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }

                        if ($ruleName == 'match') {
                            if (trim($dataField[$fieldName]) != trim($dataField[$ruleValue])) {
                                $this->setErrors($fieldName, $ruleName);
                                $checkValidate = false;
                            }
                        }

                        if ($ruleName == 'unique') {

                            $tableName = null;
                            $fieldSelect = null;

                            if (!empty($ruleArr[1])) {
                                $tableName = $ruleArr[1];
                            }

                            if (!empty($ruleArr[2])) {
                                $fieldSelect = $ruleArr[2];
                            }

                            if (!empty($tableName) && !empty($fieldSelect)) {
                                if (count($ruleArr) == 3) {
                                    $countEmail = $this->db->query("SELECT $fieldSelect FROM $tableName WHERE $fieldSelect='$dataField[$fieldSelect]'")->rowCount();
                                    if (!empty($countEmail)) {
                                        $this->setErrors($fieldName, $ruleName);
                                    }
                                } else if (count($ruleArr) == 4 && preg_match('~.+~is', $ruleArr[3])) {
                                    if (!empty($ruleArr[3])) {
                                        $conditionWhere = $ruleArr[3];
                                        $conditionWhere = str_replace('=', '<>', $conditionWhere);
                                        $countEmail = $this->db->query("SELECT $fieldSelect FROM $tableName WHERE $fieldSelect='$dataField[$fieldSelect]' AND $conditionWhere ")->rowCount();
                                        if (!empty($countEmail)) {
                                            $this->setErrors($fieldName, $ruleName);
                                        }
                                    }
                                }
                            }
                        }

                        // call back -> viet sau
                    }
                }
            }
        }

        $sessionKey = Session::isInvalid();
        Session::flash($sessionKey . '_errors', $this->errors());
        Session::flash($sessionKey . '_old', $this->getFields());

        return $checkValidate;
    }

    public function errors($fieldName = '')
    {
        if (empty($fieldName)) {
            if (!empty($this->__errors)) {
                $errorsArr = [];
                foreach ($this->__errors as $key => $item) {
                    $errorsArr[$key] = reset($item);
                }
                return $errorsArr;
            }

            return false;
        }

        if (!empty($this->__errors[$fieldName])) {
            return reset($this->__errors[$fieldName]);
        }

        return false;
    }

    public function setErrors($fieldName, $ruleName)
    {
        $this->__errors[$fieldName][$ruleName] = $this->__messages[$fieldName . '.' . $ruleName];
    }
}
