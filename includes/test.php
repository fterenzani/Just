<?php

require 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

class Form implements IteratorAggregate
{

    public $errors = array();

    protected $id;

    protected $inputs = array();

    function __construct() {


    }


    function validate() {
        $isValid = true;
        foreach ($this->inputs as $input) {
            if (!$input->validate()) {
                $isValid = false;
            }
        }
        return $isValid;
    }

    function getIterator() {
        return new ArrayIterator($this->inputs);
        
    }

    function set($name, $data = array()) {
        $input = new FormInput($name);
        $input->setData($data);
        $this->inputs[$name] = $input;
        return $input;
    }

    function fillOut($values) {
        foreach ($this->inputs as $name => $input) {
            if (isset($values[$name])) {
                $input->setValue($values[$name]);
            }
        }
    }

    function getValues() {

        $values = array();

        foreach ($this->inputs as $name => $input) {
            if (!$input->ignore)
                $values[$name] = $input->getValue();
        }

        return $values;

    }

    function getValue($name) {
        if (isset($this->inputs[$name])) {
            return $this->inputs[$name]->getValue();
        }
        throw new Exception("No input name \"$name\" in this form");
    }

}

class FormInput
{

    public $label;
    public $isRequired = false;
    public $ignore = false;
    public $error;

    protected $name;
    protected $render = 'input';
    protected $data = array();
    protected $value;
    protected $validators = array();
    protected $filters = array();
    protected $isHtmlAllowed = false;

    protected $requiredMessage = null;

    function __construct($name) {
        $this->name = $name;

        $this->label = ucfirst(str_replace('_', ' ', $name));

    }
    
    function setRequired($bool = true, $message = null) {
        $this->isRequired = (bool) $bool;
        if (!$bool) {
            $this->requiredMessage = null;

        } elseif ($message) {
            $this->requiredMessage = $message;

        }

        return $this;
       
    }

    function allowHtml($bool = true) {
        $this->isHtmlAllowed = (bool) $bool;

    }

    function setData($data) {
        $this->data = $data;

        if (isset($data['label'])) {
            $this->label = $data['label'];
        }

        return $this;
    }

    function setValue($value) {
        $this->value = $value;
        return $this;
    }

    function getValue() {
        return $this->isHtmlAllowed? $this->value: htmlspecialchars($this->value, ENT_QUOTES);
    }

    function setIgnore($bool = true) {
        $this->ignore = (bool) $bool;
        return $this;
    }

    function setRender($render) {
        $this->render = $render;
        return $this;
    }

    function addValidator($callback, $message = NULL) {
        $this->validators[] = array(
            'callback' => $callback,
            'message' => $message,
        );
        return $this;

    }

    function addFilter($callback) {
        $this->filters[] = $callback;
    }

    function validate() {
        
        if ($this->value && $this->filters) {
            foreach ($this->filters as $callback) {
                $this->value = call_user_func($callback, $this->value);
            }            
        }

        if ($this->isRequired) {
            array_unshift($this->validators, array(
                'callback' => new Zend_Validate_NotEmpty,
                'message' => $this->requiredMessage,
            ));
        }

        foreach ($this->validators as $validator) {

            if ($validator['callback'] instanceof Zend_Validate_Abstract) {

                if (!$validator['callback']->isValid($this->value)) {

                    if ($validator['message']) {
                        $this->error = $validator['message'];

                    } else {
                            $this->error = current($validator['callback']->getMessages());
                    }

                    return false;
                }

            } elseif (!call_user_func($validator['callback'], $this->value)) {

                $this->errors[] = $validator['message'];

                return false;

            }
        }

    }

    function isHidden() {
        return isset($this->data['type']) && $this->data['type'] === 'hidden';
    }

    function render() {
        $func = 'form_' . $this->render;
        $value = $this->isHtmlAllowed? htmlspecialchars($this->getValue(), ENT_QUOTES):
                $this->getValue();
        $func($this->name, $value, $this->data);
    }

}



$form = new Form;
$form->set('username')
        ->setRequired()
        ->addValidator(new Zend_Validate_Alnum);

$form->set('email')
        ->setRequired()
        ->addValidator(new Zend_Validate_EmailAddress);

$form->set('password', array('type' => 'password'))
        ->setRequired()
        ->addValidator(new Zend_Validate_StringLength(array('min' => 8)));

$form->set('retype_password', array('type' => 'password'))
        ->setRequired()
        ->addValidator(new Zend_Validate_Identical(@$_POST['password']))
        ->setIgnore(true);

$form->fillOut($_POST);

if ($_POST && $form->validate()) {
    
}

//$form->setEmbed('tags', $tags);


//render_partial('form.php', array('form' => $form));

//print_r($form->getValues());
//print_r($form->getValue('username'));
//
//$form = new Form;
//$form->set('title');
//$form->set('content')->setRender('textarea');



?>

<?php function form_input($name, $value, $data) { ?>
    <input
        name="<?php echo $name ?>"
        id="<?php echo $name ?>"
        value="<?php echo $value ?>"
        type="<?php echo isset($data['type'])? $data['type']: 'text' ?>" />
<?php } ?>

<?php function form_textarea($name, $value, $data) { ?>
    <textarea
        name="<?php echo $name ?>"
        id="<?php echo $name ?>"><?php echo $value ?></textarea>
<?php } ?>

<?php function form_choice_select($name, $value, $data) { ?>
    <select
        name="<?php echo $name ?>"
        id="<?php echo $name ?>"
        <?php if ($data['multiple']): ?>
        multiple="multiple"
        <?php endif ?>>
        <?php foreach ($data['options'] as $key => $option): ?>
            <option
                value="<?php echo $key ?>"
                <?php if ($key == $value): ?>
                seleted="selected"
                <?php endif ?>>
                <?php echo $option ?></option>
        <?php endforeach ?>
    </select>
<?php } ?>

<?php function form_choice_input($name, $value, $data) { ?>
    <?php foreach ($data['options'] as $key => $option): ?>
    <label>
    <input
        name="<?php echo $name ?>"
        id="<?php echo $name ?>"
        type="<?php echo $data['type'] ?>"
        value="<?php echo $key ?>"
        <?php if ($key == $value): ?>
        checked="checked"
        <?php endif ?>
        />
    <?php echo $option ?></label><br />
    <?php endforeach ?>
<?php } ?>


    <form method="post" action="">
        <table>



<?php if ($form->errors): ?>
<tr>
    <td colspan="2" class="form_errors">
        <ul>
            <?php foreach ($form->errors as $error): ?>
            <li><?php echo $error ?></li>
            <?php endforeach ?>
        </ul>
    </td>
</tr>
<?php endif ?>
<?php foreach ($form as $input): ?>

    <?php if ($input->isHidden()): ?>
        <?php $input->render(); continue; ?>
    <?php endif ?>

    <tr>
        <th class="label">
            <?php echo $input->label ?>
            <?php if ($input->isRequired): ?>
                <b>*</b>
            <?php endif ?>
        </th>
        <td class="field">
            <?php $input->render() ?>
            <?php if ($input->error): ?>
                <div class="input_error"><?php echo $input->error ?></div>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>

    <tr>
        <td colspan="2">
            <input type="submit">
        </td>
    </tr>

    </table>
    </form>

require 'modules/articoli/form.php';

if ($form->validate()) {
    $articolo = $db->getTable('Articolo')->set($form->getValues());
    
    if (isset($params['id'])) {
        $articolo->where('id = ?', $params['id'])->update();
        redirect_for('articoli', array('id' => $params['id']));

    } else {
        $articolo->insert();
        redirect_for('articoli', array('id' => $db->lastInserId()));
        
    }

}

render_partial('form.php', $form);
