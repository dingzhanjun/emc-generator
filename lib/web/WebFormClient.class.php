<?php


// {{{ Form classes

class BaseWebFormField
{
    private $node = null;
    private $name = null;
    private $value = null;
    protected $options = array();
    private $charset = null;


    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        if ($name !== null)
            $this->setName($name);

        // Set charset *before* calling addNode()
        if ($charset !== null)
            $this->setCharset($charset);

        if ($node !== null) {
            $this->setNode($node);
            $this->addNode($node, $xpath);
        }
    }


    public function getNode()
    {
        return $this->node;
    }


    public function setNode($node)
    {
        $this->node = $node;
    }

    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }


    public function getCharset()
    {
        return $this->charset;
    }


    public function setCharset($charset)
    {
        $this->charset = $charset;
    }


    public function getValue()
    {
        return $this->value;
    }


    public function setValue($value, $validate = true)
    {
        $this->value = $value;
    }


    public function addNode($node, $xpath)
    {
    }


    protected function decodeValue($value)
    {
        $value = html_entity_decode($value, ENT_COMPAT, 'UTF-8');
        if ($this->getCharset())
            $value = iconv('UTF-8', $this->getCharset().'//TRANSLIT', $value);
        return $value;
    }


    public function getPostValues()
    {
        return array($this->getName() => $this->getValue());
    }


    public function getType()
    {
        $node = $this->getNode();
        $type = $node->nodeName;

        if ($node->hasAttribute('type'))
            $type .= '-'.$node->getAttribute('type');

        if ($node->hasAttribute('multiple'))
            $type .= '-multiple';

        return $type;
    }


    public static function createFromData($name, $type = null, $data = null, $charset = null)
    {
        if ($type === null)
            $type = 'input';

        switch ($type) {
          case 'input':
            $field = new WebFormInput($name, null, null, $charset);
            break;
          case 'checkbox':
            $field = new WebFormCheckbox($name, null, null, $charset);
            $field->setDefaultValue($data);
            break;
          case 'radio':
            $field = new WebFormRadio($name, null, null, $charset);
            break;
          case 'textarea':
            $field = new WebFormTextarea($name, null, null, $charset);
            break;
          case 'select':
            $field = new WebFormSelect($name, null, null, $charset);
            break;
          case 'select-multiple':
            $field = new WebFormSelectMultiple($name, null, null, $charset);
            break;
          default:
            throw new Exception("Unsupported WebForm type '$type'");
        }

        return $field;
    }


    public static function createFromNode($name, $node, $xpath, $charset)
    {
        $tag = $node->nodeName;
        $type = $node->getAttribute('type');

        if ($tag === 'input' && $type === 'checkbox')
            return new WebFormCheckbox($name, $node, $xpath, $charset);

        if ($tag === 'input' && $type === 'radio')
            return new WebFormRadio($name, $node, $xpath, $charset);

        if ($tag === 'input' && $type === 'image')
            return new WebFormSubmit($name, $node, $xpath, $charset);

        if ($tag === 'textarea')
            return new WebFormTextarea($name, $node, $xpath, $charset);

        if ($tag === 'select' && $node->hasAttribute('multiple'))
            return new WebFormSelectMultiple($name, $node, $xpath, $charset);

        if ($tag === 'select')
            return new WebFormSelect($name, $node, $xpath, $charset);

        if ($tag === 'button')
            return new WebFormButton($name, $node, $xpath, $charset);

        return new WebFormInput($name, $node, $xpath, $charset);
    }
}


class SingleWebFormField extends BaseWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        $this->setValue('', false);
        parent::__construct($name, $node, $xpath, $charset);
    }
}


class SingleChoice extends SingleWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function setValue($value, $validate = true)
    {
        if ($validate && !in_array($value, $this->options))
            throw new Exception("Invalid value '$value' for field '{$this->getName()}', allowed values: '".implode("', '", $this->options)."'");
        return parent::setValue($value, $validate);
    }
}


class MultipleWebFormField extends BaseWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        $this->setValue(array(), false);
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addValue($value)
    {
        $values = $this->getValue();
        $values[] = $value;
        $this->setValue($values);
    }
}


class MultipleChoice extends MultipleWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function setValue($value, $validate = true)
    {
        if ($validate && is_array($value)) {
            foreach ($value as $element)
                if (!in_array($element, $this->options) && $element != false)
                   throw new Exception("Invalid value '$element' for field '{$this->getName()}', allowed values: '".implode("', '", $this->options)."'");
        } elseif ($validate && !in_array($value, $this->options) && $value != false) {
            throw new Exception("Invalid value '$value' for field '{$this->getName()}', allowed values: '".implode("', '", $this->options)."'");
        }
        return parent::setValue($value, $validate);
    }
}

class WebFormInput extends SingleWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addNode($node, $xpath)
    {
        $this->setValue($this->decodeValue($node->getAttribute('value')));
    }
}

class WebFormCheckbox extends MultipleChoice
{

    private $default_value = null;
    private $count = 0;


    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function isMultiple()
    {
        return $this->count > 1;
    }


    public function getDefaultValue()
    {
        return $this->default_value;
    }


    public function setDefaultValue($value)
    {
        $this->default_value = $value;
    }


    public function setValue($value, $validate = true)
    {
        if (($value === true) && $this->isMultiple())
            throw new Exception("True values not supported by multiple checkbox '{$this->getName()}'");

        if (($value === true || $value === false) && $this->getDefaultValue() === null)
            throw new Exception("True/False values not supported by checkbox '{$this->getName()}': no default value specified");

        if ($value === true)
            parent::setValue(array($this->getDefaultValue()), $validate);
        elseif ($value === false)
            parent::setValue(array(), $validate);
        else
            parent::setValue($value, $validate);
    }


    public function addNode($node, $xpath)
    {
        if ($node->hasAttribute('value'))
            $value = $this->decodeValue($node->getAttribute('value'));
        else
            $value = 'on';
        $this->options[] = $value;
        ++$this->count;

        if ($this->count === 1)
            $this->setDefaultValue($value);
        if ($node->hasAttribute('checked'))
            $this->addValue($value);
    }
}




class WebFormButton extends MultipleWebFormField
{
    private $default_value = null;
    private $count = 0;


    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function getDefaultValue()
    {
        return $this->default_value;
    }


    public function setDefaultValue($value)
    {
        $this->default_value = $value;
    }


    public function getValue()
    {
        if ($this->count == 1)
            return parent::getValue();
        else
            return parent::getValue();
    }


    public function setValue($value, $validate = true)
    {
        if ($value === true)
            parent::setValue(array($this->getDefaultValue()), $validate);
        elseif ($value === false)
            parent::setValue(array(), $validate);
        else
            parent::setValue($value, $validate);
    }


    public function addNode($node, $xpath)
    {
        $this->setValue($this->decodeValue($node->getAttribute('value')));
    }
}

class WebFormRadio extends SingleChoice
{

    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addNode($node, $xpath)
    {
        $value = $this->decodeValue($node->getAttribute('value'));
        $this->options[] = $value;
        if ($node->hasAttribute('checked'))
            $this->setValue($value);
    }
}


class WebFormSelect extends SingleChoice
{
    public function __construct($name = null, $node = null, $xpath = null , $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addNode($node, $xpath)
    {
        foreach($xpath->query('.//option', $node) as $option) {
            if ($option->hasAttribute('value'))
                $this->options[] = $this->decodeValue($option->getAttribute('value'));
            else
                $this->options[] = $this->decodeValue($option->nodeValue);
        }

        $nodes = $xpath->query('.//option[@selected]', $node);
        if ($nodes->length > 1)
            //throw new Exception("Multiple selected options on simple select");
            Logger::warning("Multiple selected options on simple select");
        if ($nodes->item(0)) {
            if ($option->hasAttribute('value'))
                $this->setValue($this->decodeValue($nodes->item(0)->getAttribute('value')));
            else
                $this->setValue($this->decodeValue($option->nodeValue));
        }
    }
}

class WebFormSelectMultiple extends MultipleChoice
{

    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addNode($node, $xpath)
    {
        foreach($xpath->query('.//option', $node) as $option) {
            if ($option->hasAttribute('value'))
                $this->options[] = $this->decodeValue($option->getAttribute('value'));
            else
                $this->options[] = $this->decodeValue($option->nodeValue);
        }
        foreach ($xpath->query('.//option[@selected]', $node) as $option)
            $this->addValue($this->decodeValue($option->getAttribute('value')));
    }
}


class WebFormTextarea extends SingleWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function addNode($node, $xpath)
    {
        $this->setValue($this->decodeValue($node->nodeValue));
    }
}


class WebFormSubmit extends BaseWebFormField
{
    public function __construct($name = null, $node = null, $xpath = null, $charset = null)
    {
        parent::__construct($name, $node, $xpath, $charset);
    }


    public function getPostValues()
    {
        return array(
            $this->getName().'.x' => rand(2, 10),
            $this->getName().'.y' => rand(2, 10),
            );
    }
}

// }}}


// {{{ Client classes

class WebFormClient extends WebClient
{
    private $body = null;
    private $doc = null;
    private $xpath = null;
    private $form = null;
    private $fields = null;
    private $charset = null;


    public function __construct()
    {
        parent::__construct();
        $this->setAutoReferer(true);
        $this->setDelay(2);
    }


    public function post($url, $post = null)
    {
        if ($post === null) {
            if ($this->getFormFields() === null)
                throw new Exception("No POST data specified for request on URL '$url'");

            $post = array();
            foreach ($this->getFormFields() as $field) {
                foreach ($field->getPostValues() as $name => $value) {
                    if (array_key_exists($name, $post))
                        throw new Exception("Duplicate POST field '$name'");
                    $post[$name] = $value;
                }
            }
        }

        return parent::post($url, $post);
    }


    public function postOrderedFields($url, $ordered_array_post)
    {
        $boundary = str_repeat('-', 28).myFunctions::generateRandomKey(12, '0123456789abcdef');

        $this->addHeader('Content-Type: multipart/form-data; boundary='.$boundary);
        $this->addHeader('Expect:');

        // 1) On insère les champs demandés
        $data = '';
        $already_inserted_fields = array();
        foreach ($ordered_array_post as $num => $field) {
            $name = $field['name'];
            $value = $field['value'];
            if (is_array($value))
                foreach ($value as $v)
                    $data .= $this->buildPostPart($boundary, $name, $v);
            else
                $data .= $this->buildPostPart($boundary, $name, $value);
            $already_inserted_fields[] = $name;
        }

        // 2) on insere les autres champs du validate
        foreach ($this->getFormFields() as $field) {
                foreach ($field->getPostValues() as $name => $value) {
                 if ( !in_array($name, $already_inserted_fields)){
                    if (is_array($value))
                        foreach ($value as $v)
                            $data .= $this->buildPostPart($boundary, $name, $v);
                    else
                        $data .= $this->buildPostPart($boundary, $name, $value);
                    }
                }
        }
        $data .= "--$boundary--\r\n";


        $this->setEncType('raw');
        return parent::post($url, $data);
    }


    public function request($url)
    {
        $body = parent::request($url);
        $this->setBody($body);
        return $body;
    }


    // {{{ Generic Accessors

    public function getBody()
    {
        return $this->body;
    }


    public function getUTF8Body()
    {
        $charset = $this->getCharset();
        if ($charset)
            return iconv($charset, 'utf-8', $this->body);
        else
            return $this->body;
    }


    public function getCharset()
    {
        if ($this->charset === null) {
            $charset_found = false;
            $ch = $this->getHandle();
            $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            if ($content_type){
                $charset = substr(strrchr($content_type, "charset="), 8);
                if( $charset)
                    $charset_found = true;
            }
            // on a pas trouvé avec Curl dans les headers http, on essaye de chercher le meta
            if (!$charset_found){
                $query = '//html/head/meta[@http-equiv="Content-Type"]';
                $nodes = $this->getXPath()->query($query);
                if ($nodes->length == 1){
                    $charset = substr(strrchr($nodes->item(0)->getAttribute('content'), "charset="), 8);
                    if( $charset)
                    $charset_found = true;
                }
            }

            if ($charset_found)
                $this->charset = $charset;
            else
                $this->charset = false;
        }

        if ($this->charset === false)
            return null;
        else
            return $this->charset;
    }


    public function setBody($body)
    {
        $this->body = $body;
        $this->doc = null;
        $this->xpath = null;
        $this->form = null;
        $this->fields = null;
        $this->charset = null;
    }


    public function getDOMDocument()
    {
        if ($this->doc === null) {
            $body = $this->getBody();
            if ($body === null)
                throw new Exception("Cannot buid DOMDocument from null body");
            $doc = new DOMDocument();
            @$doc->loadHTML($body);
            $this->setDOMDocument($doc);
        }
        return $this->doc;
    }


    public function setDOMDocument($doc)
    {
        $this->doc = $doc;
    }


    public function getXPath()
    {
        if ($this->xpath === null)
            $this->setXPath(new DOMXPath($this->getDOMDocument()));
        return $this->xpath;
    }


    public function setXPath($xpath)
    {
        $this->xpath = $xpath;
    }


    public function getForm()
    {
        return $this->form;
    }
    public function setForm($form)
    {
        $this->form = $form;
    }


    public function getFormFields()
    {
        return $this->fields;
    }


    public function setFormFields($fields)
    {
        $this->fields = $fields;
    }


    public function getFormAction()
    {
        return $this->getForm()->getAttribute('action');
    }

    // }}}

    // {{{ Form handling functions

    public function appendField($name, $type = null, $data = null)
    {
        if (array_key_exists($name, $this->fields))
            throw new Exception("Cannot add field '$name', field already exists");

        $this->fields[$name] = BaseWebFormField::createFromData($name, $type, $data, $this->getCharset());
    }


    public function insertField($prev, $name, $type = null, $data = null)
    {
        if (array_key_exists($name, $this->fields))
            throw new Exception("Cannot add field '$name', field already exists");
        if ($prev && !array_key_exists($prev, $this->fields))
            throw new Exception("Cannot insert after field '$prev', field doesn't exists");

        $fields = $this->fields;
        $this->fields = array();
        if (!$prev)
            $this->fields[$name] = BaseWebFormField::createFromData($name, $type, $data, $this->getCharset());

        foreach ($fields as $f_name => $f_field) {
            $this->fields[$f_name] = $f_field;
            if ($f_name === $prev)
                $this->fields[$name] = BaseWebFormField::createFromData($name, $type, $data, $this->getCharset());
        }
    }


    public function removeField($name)
    {
        if (!array_key_exists($name, $this->fields))
            throw new Exception("Cannot remove field '$name', field doesn't exists");

        unset($this->fields[$name]);
    }


    public function getData()
    {
        $data = array();
        foreach ($this->getFormFields() as $name => $field)
            $data[$name] = $field->getValue();
        return $data;
    }


    public function getFields()
    {
        return array_keys($this->getFormFields());
    }


    public function buildFormQuery($param)
    {
        if (!is_array($param))
            return $param;

        $form = array();
        foreach ($param as $name => $value)
            $form[] = "@$name='$value'";

        $query = "//form";
        if ($form)
            $query .= '['.implode(' and ', $form).']';

        return $query;
    }


    public function loadForm($query)
    {
        $query = $this->buildFormQuery($query);
        $nodes = $this->getXPath()->query($query);
        if (!$nodes->length)
            throw new Exception("No Form found: '$query'");
        if ($nodes->length !== 1)
            throw new Exception("Multiple Forms found: '$query'");
        return $nodes->item(0);
    }


    public function loadFormFields($form_node)
    {
        $xpath = $this->getXPath();
        $query = ".//*[name()='input' or name()='select' or name()='textarea' or name()='button']";
        $node_list = $xpath->query($query, $form_node);

        $fields = array();
        foreach ($node_list as $node) {
            $name = $node->getAttribute('name');
            if (!$name)
                continue;

            if (array_key_exists($name, $fields))
                $fields[$name]->addNode($node, $xpath);
            else
                $fields[$name] = BaseWebFormField::createFromNode($name, $node, $xpath, $this->getCharset());
        }

        return $fields;
    }

    // }}}

    // {{{ Client functions

    public function load($query)
    {
        $form_node = $this->loadForm($query);
        $this->setForm($form_node);
        $this->setFormFields($this->loadFormFields($form_node));

        if ($form_node->hasAttribute('enctype'))
            $this->setEncType($form_node->getAttribute('enctype'));
    }


    public function validate($data, $check_new = false, $check_type = true, $check_missing = true)
    {
        foreach ($this->getFormFields() as $name => $field) {
            $type = $field->getType();

            if (!array_key_exists($name, $data)) {
                if ($check_new && !array_key_exists($name, $data))
                    throw new Exception("Form validation failed: unexpected field '$name/$type'");
                else
                    continue;
            }

            if ($check_type && $field->getType() !== $data[$name])
                throw new Exception("Form validation failed: expected type '{$data[$name]}' for field '$name/$type'");

            unset($data[$name]);
        }

        if ($check_missing)
            foreach ($data as $name => $type)
                throw new Exception("Form validation failed: missing field '$name/$type'");
    }


    public function fill($data, $validate = true)
    {
        foreach ($this->fields as $name => $field)
            if (array_key_exists($name, $data))
                $this->fields[$name]->setValue($data[$name], $validate);
    }


    public function info($query = '//form')
    {
        $xpath = $this->getXPath();
        $forms = $xpath->query($query);

        if (!$forms->length) {
            echo "No form found !\n";
            return;
        }

        if ($forms->length == 1)
            echo "1 form found:\n";
        else
            echo $forms->length, " forms found:\n";

        foreach ($forms as $index => $form_node) {
            if ($forms->length > 1)
                echo "Form $index:\n";

            $details = array();

            foreach ($form_node->attributes as $name => $attr)
                $details[$name] = $attr->value;

            $details['_data'] = "\n";

            if ($details['id'] && $details['name'])
                $query = "array('id' => '{$details['id']}', 'name' => '{$details['name']}')";
            elseif ($details['id'])
                $query = "array('id' => '{$details['id']}')";
            elseif ($details['name'])
                $query = "array('name' => '{$details['name']}')";
            elseif ($details['action'])
                $query = "array('action' => '{$details['action']}')";
            else
                $query = '';

            if ($query)
                $details['_data'] .= "        \$client->load({$query});";


            $fields = array();
            $max = 0;
            foreach ($this->loadFormFields($form_node) as $name => $field) {
                $len = mb_strlen($name);
                $fields[$name] = array(
                    'len' => $len,
                    'type' => $field->getType(),
                    );
                $max = max($max, $len);
            }

            $details['_data'] .= "\n        \$client->validate(array(\n";
            foreach ($fields as $name => $data) {
                $details['_data'] .= "                '{$name}'";
                $details['_data'] .= str_repeat(' ', $max - $data['len']);
                $details['_data'] .= " => '{$data['type']}',\n";
            }
            $details['_data'] .= "                ));";
            $details['_data'] .= "\n        \$post = \$client->getData();";


            foreach ($details as $name => $value) {
                echo "  $name: ";
                $col = 12 - mb_strlen($name);
                if ($col > 0)
                    echo str_repeat(' ', $col);
                echo $value, "\n";
            }
        }
    }


   public function getField($name)
   {
       if (!array_key_exists($name, $this->fields))
           throw new Exception("Cannot add field '$name', field already exists");

       return $this->fields[$name];
   }


   public function hasField($name)
   {
       if (array_key_exists($name, $this->fields))
           return true;
       else
           return false;
   }
    // }}}
}

// }}}


?>
