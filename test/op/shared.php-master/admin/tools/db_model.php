<?php


    $db_skins ['object'] = '
    class {name}
    {
        public $id;{declare}

        public function __construct (
        $id=null{construct}
        )
        {
            $this->id = $id;{assign}
        }
    }
    ';

    $db_skins ['object_declare_item'] = '
        public ${name};';

    $db_skins ['object_construct_item'] = ',
        ${name}=null';

    $db_skins ['object_assign_item'] = '
            $this->{name} = ${name};';


    $db_skins ['table'] = '
        class {class}s extends \db\table
        {
            public function __construct ()
            {
                parent::__construct (\'{namespace}.{class}s\', \'{table}\');
                {fields}
            }
            public function create (&$values, $action=\db\action::select)
            {
                parent::create ($values, $action);
                return new \{namespace}\{class} (
                $values->id{assigns}
                );
            }
        }
    ';

    $db_skins ['table_field_item'] = '
                #### field: {name} ####
                $this->fields->{name} = new \db\field (\'{name}\', \db\type::{type});
                $this->fields->{name}->foreign = \'{foreign}\';
                $this->fields->{name}->required = {required};
                $this->fields->{name}->readonly = {readonly};
                $this->fields->{name}->insert = {insert};
                $this->fields->{name}->select = {select};
                $this->fields->{name}->update = {insert};
                $this->fields->{name}->remote = {remote};
                $this->fields->{name}->report = {report};
                $this->fields->{name}->register = {register};
                $this->fields->{name}->pattern = \'{pattern}\';
                $this->fields->{name}->default = \'{default}\';
                $this->fields->{name}->unique = {unique};
                $this->fields->{name}->minimal = \'{minimal}\';
                $this->fields->{name}->maximal = \'{maximal}\';
                $this->fields->{name}->caption = \'{caption}\';
                $this->fields->{name}->input = \'{input}\';
                $this->fields->{name}->length = \'{length}\';
                $this->fields->{name}->culture = {culture};
                $this->fields->{name}->event->insert = {oninsert};
                $this->fields->{name}->event->select = {onselect};
                $this->fields->{name}->event->update = {oninsert};
    ';

    $db_skins ['table_assign_item'] = ',
                $values->{name}';

    form_open ();
    form_add_hidden ('apage', $apage);
    form_add_label ('ბაზის ობიექტური მოდელის გენერირება');
    form_add_spacer ();
    form_add_edit ('model[namespace]', 'ნეიმსფეისი (name)', $model['namespace']);
    form_add_edit ('model[tables]', 'ცხრილები (prefix_*,table)', $model['tables']);
    form_add_submit ('გენერაცია');
    form_close ();

    if ($model['namespace'] && $model['tables'])
    {
        $result = \db\model::model ($model['namespace'], $model['tables']);
    }

?>


