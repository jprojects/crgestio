<?php
/**
 * @version     1.0.0 Afi Framework $
 * @package     Afi Framework
 * @copyright   Copyright © 2014 - All rights reserved.
 * @license	    GNU/GPL
 * @author	    kim
 * @author mail kim@afi.cat
 * @website	    http://www.afi.cat
 *
 */

defined('_Afi') or die ('restricted access');

class Html
{
    /**
     * Method to load a form
     * @param $form string the form xml name
    */
    public function getForm($form)
    {
        $output = simplexml_load_file('component/forms/'.$form.'.xml');
        return $output;
    }

    /**
     * Method to render a complete table
    */
    public function renderTable($id, $key, $data, $fields=array(), $columns=array())
    {
        $app    = factory::getApplication();
        $config = factory::getConfig();

        $view    = $app->getVar('view');
        $page  	 = $app->getVar('page', 1);
        $orderDir= $app->getVar('orderDir', 'asc');
        $colDir  = $app->getVar('colDir', $key);

        $html  = '';
        $html .= '<div class="table-responsive">';
        $html .= '<table id="'.$id.'" class="table table-striped table-bordered mb-5">';
        $html .= '<input type="hidden" name="orderDir" value="'.$orderDir.'">';
        $html .= '<input type="hidden" name="colDir" value="'.$colDir.'">';
        $html .= '<thead class="thead-dark">';
        $html .= '<tr>';
        $html .= '<th width="1%" data-orderable="false"><input type="checkbox" id="selectAll"></th>';
        $i = 0;
        foreach($columns as $column) {
            //multidemensional array can contain name => name of column, width => column width
            $orderDir == 'asc' ? $dir = 'desc' : $dir = 'asc';
            $html .= '<th width="'.$column['width'].'"><a href="index.php?view='.$view.'&page='. $page .'&orderDir='.$dir.'&colDir='.$fields[$i]['name'].'">'.$column['name'].'&nbsp;';
            if($fields[$i]['name'] == $colDir) { $html .= '<i class="fa fa-sort-'.$dir.'"></i>'; } else { $html .= '<i class="fa fa-sort-asc"></i>'; }
            $html .= '</a></th>';
            $i++;
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach($data as $d) {
            //multidemensional array can contain name => name of column in db, editable => true/false create a link to edit view
            $html .= '<tr class="item" data-id="'.$d->{$key}.'">';
            $html .= '<td>';
			$html .= '<input type="checkbox" name="cd" data-id="'.$d->{$key}.'">';
			$html .= '</td>';
            foreach($fields as $field) {
                $field['editable'] ? $text = '<a href="index.php?view='.$view.'&layout=edit&id='.$d->{$key}.'">'.$d->{$field['name']}.'</a>' : $text = $d->{$field['name']};
                $html .= '<td>'.$text.'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '<tfoot class="thead-dark">';
        $html .= '<tr>';
        $html .= '<th width="1%" data-orderable="false"></th>';
        $i = 0;
        foreach($columns as $column) {
            //multidemensional array can contain name => name of column, width => column width
            $orderDir == 'asc' ? $dir = 'desc' : $dir = 'asc';
            $html .= '<th width="'.$column['width'].'"><a href="index.php?view='.$view.'&page='. $page .'&orderDir='.$dir.'&colDir='.$fields[$i]['name'].'">'.$column['name'].'&nbsp;';
            if($fields[$i]['name'] == $colDir) { $html .= '<i class="fa fa-sort-'.$dir.'"></i>'; } else { $html .= '<i class="fa fa-sort-asc"></i>'; }
            $html .= '</a></th>';
            $i++;
        }
        $html .= '</tr>';
        $html .= '</tfoot>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Method to render a complete table
    */
    public function renderReportTable($id, $key, $fields=array(), $columns=array(), $group)
    {
        $app   = factory::getApplication();
        $view  = $app->getVar('view');
        $model = $app->getModel($view);
        $count = count($columns);

        $groups  = $model->getGroups($group);

        $html  = '';
        $html .= '<div class="table-responsive">';
        $html .= '<table id="'.$id.'" class="table table-striped table-bordered">';
        $html .= '<thead>';
        $html .= '<tr>';
        foreach($columns as $column) {
            $html .= '<th>'.$column.'</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach($groups as $group) {
            $html .= '<tr class="item-group">';
            for($i=0;$i<$count;$i++) {
                $html .= '<td><b>'.$group->nom.'</b></td>';
            }
            $html .= '</tr>';
            foreach($model->getListGrouped('i.projecteId', $group->projecte_id) as $d) {
                $html .= '<tr>';
                foreach($fields as $field) {
                    $html .= '<td>'.$d->{$field}.'</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';
        // $html .= '<script>$(document).ready(function() { var dataTable = $("#'.$id.'").DataTable({ "order": [[1, "asc"]], "paging": false, rowReorder: true, responsive: { details: false } });';
        // $html .= '});</script>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Method to load a form
     * @param $form string the form xml name
    */
    public function renderFilters($form, $view, $collapse=true)
    {
    	$app    = factory::getApplication();
        $lang   = factory::getLanguage();
        $db     = factory::getDatabase();
        $user   = factory::getUser();

        $fields = simplexml_load_file('component/forms/filters_'.$form.'.xml');

        $html   = '';

        if($collapse == true) {
            $html  .= '<a class="btn btn-primary mb-2" data-toggle="collapse" href="#collapseFilters" role="button" aria-expanded="false" aria-controls="collapseFilters">Filtres</a>';
            $html  .= '<div class="collapse" id="collapseFilters">';
        }
        $html  .= '<div class="form-inline my-3">';
        $html  .= '<input type="hidden" name="view" value="'.$view.'">';

        $i = 0;
        foreach($fields as $field) {

            if($field->getName() == "field"){
                if($i > 0) { $html .= '&nbsp;'; }

                if($field[$i]->type == 'text') {

                    $html .= "<div id='".$field[$i]->name."-field' class='mb-3'>";
                    $html .= "<div class='controls'>";
                    $html .= "<input type='".$field[$i]->type."' id='".$field[$i]->id."' value='".$_GET[''.$field[$i]->name.'']."' name='".$field[$i]->name."' data-message='".$lang->get($field[$i]->message)."' placeholder='".$lang->get($field[$i]->placeholder)."' class='form-control ".$field[$i]->clase."' autocomplete='off'>";
                    $html .= "</div>";
                    $html .= "</div>";
                }
                if($field[$i]->type == 'date') {
                    $html .= "<div id='".$field[$i]->name."-field' class='mb-3'>";
                    $html .= "<div class='input-group date' id='".$field[$i]->id."-icon'>";
                    $html .= "<input type='text' id='".$field[$i]->id."' value='".$_GET[''.$field[$i]->name.'']."' name='".$field[$i]['name']."' data-message='".$lang->get($field[$i]->message)."' class='form-control' autocomplete='off'>";
                    $html .= "<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "<script>document.addEventListener('DOMContentLoaded', function(event) { $(function(){ $('#".$field[$i]->id."-icon').datetimepicker({sideBySide: false,format: '".$field[$i]->format."'}); }); });</script>";
                }
                if($field[$i]->type == 'list') {
                    $html .= "<div id='".$field[$i]->name."-field' class='mb-3'>";
                    $html .= "<div class='controls'>";
                    $html .= "<select id='".$field[$i]->id."' name='".$field[$i]['name']."' class='form-control ".$field[$i]->classe."' autocomplete='off'>";

                    foreach($field[$i]->option as $option) {
                        $_GET[''.$field[$i]->name.''] == $option['value'] ? $selected = "selected='selected'" : $selected = "";
                        $html .= "<option value='".$option['value']."' $selected>".$lang->get($option[0])."</option>";
                    }

                    if($field[$i]->query != '') {
                        $query = str_replace('{userid}', $user->id, $field[$i]->query);
                        if($user->projects != '*') {
                            $query = str_replace('{user_projects}', ' WHERE projecte_id IN ('.$user->projects.') ', $field[$i]->query);
                        } else {
                            $query = str_replace('{user_projects}', '', $field[$i]->query);
                        }
                        $db->query($query);
                        $options = $db->fetchObjectList();
                        $value = $field[$i]->value;
                        $key = $field[$i]->key;
                        foreach($options as $option) {
                            $_GET[''.$field[$i]->name.''] == $option->$key ? $selected = "selected='selected'" : $selected = "";
                            $html .= "<option value='".$option->$key."' $selected>".$option->$value."</option>";
                        }
                    }

                    $html .= "</select>";
                    $html .= "</div>";
                    $html .= "</div>";
                }

            }
        	$i++;
        }

        $html .= '&nbsp;<button class="btn btn-success" type="submit">'.$lang->get('CW_SEARCH').'</button>';
        $html .= '</div>';
        
        if($collapse == true) {
            $html .= "</div>";
        }

        return $html;
    }

    public function renderButtons($form, $view)
    {
    	$app    = factory::getApplication();
        $db     = factory::getDatabase();
        $lang   = factory::getLanguage();
        $user   = factory::getUser();

        $fields = simplexml_load_file('component/forms/filters_'.$form.'.xml');
        $html = "";
        $i = 0;

        //get permisos
        $db->query('SELECT permisos FROM #_usergroups_map WHERE usergroup_id = '.$user->_level);
        $permisos = json_decode($db->loadResult());

        foreach($fields as $field) {

            //check permissions...
            if($field[$i]->id == 'btn_new' && $permisos->new == 0) { continue; }
            if($field[$i]->id == 'btn_edit' && $permisos->edit == 0) { continue; }
            if($field[$i]->id == 'btn_delete' && $permisos->delete == 0) { continue; }

            if($field->getName() == "button"){
                $field[$i]->icon == "" ? $icon = "" : $icon = "<i class='fa ". $field[$i]->icon. "'></i>&nbsp;";
                $field[$i]->view == "" ? $view = "" : $view = "data-view='". $field[$i]->view. "'";
                $field[$i]->target == "" ? $target = "" : $target = "target='". $field[$i]->target. "'";
                $color = isset($field[$i]->color) ? $field[$i]->color : 'success';

                if($field[$i]->onclick != '') { $click = 'onclick="'.$field[$i]->onclick.'"'; } else { $click = ''; }
                $html .= '&nbsp;<a href="'. $field[$i]->href .'" '.$target.' id="'. $field[$i]->id .'" '.$click.' '.$view.'  class="btn btn-' . $color . ' ' . $field[$i]->class . '" >' . $icon . $field[$i]->label . '</a>';
            }

        	$i++;
        }

        return $html;
    }

    /**
     * Method to render a input box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getTextField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
				$field[0]->readonly == 'true' ? $readonly = "readonly='true'" : $readonly = "";
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = 'onchange="'.$field[0]->onchange.'"' : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $field[0]->onkeyup != "" ? $onkeyup = " onkeyup='".$field[0]->onkeyup."'" : $onkeyup = "";
                if($field[0]->type != 'hidden') $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->type != 'hidden' && $field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                if($field[0]->type != 'hidden' && $field[0]->label != "") $html .= "<div class='controls'>";
                $html .= "<input type='".$field[0]->type."' id='".$field[0]->id."' value='".str_replace("'","&#39;",$default)."' name='".$field[0]->name."'";
                if($field[0]->type != 'hidden') $html .= $disabled." ".$onchange." ".$required." ".$onkeyup." ".$readonly." placeholder='".$lang->get($field[0]->placeholder)."' class='form-control ".$field[0]->clase."' autocomplete='off'";
                $html .= ">";
                //if($field[0]->type != 'hidden') $html .= "<span id='".$field[0]->name."-msg'></span>";
                if($field[0]->type != 'hidden' && $field[0]->label != "") $html .= "</div>";
                if($field[0]->type != 'hidden') $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render an email field
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getEmailField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->onkeyup != "" ? $onkeyup = " onkeyup='".$field[0]->onkeyup."'" : $onkeyup = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                if($field[0]->label != "") $html .= "<div class='controls'>";
                $html .= "<input type='".$field[0]->type."' name='".$field[0]->name."' style='display:none;' />";
                $html .= "<input type='".$field[0]->type."' id='".$field[0]->id."' value='".$default."' name='".$field[0]->name."'";
                $html .= $disabled.' '.$required.' data-error="'.$lang->get($field[0]->message).'" '.$onchange.$onkeyup.' placeholder="'.$lang->get($field[0]->placeholder).'" class="form-control '.$field[0]->clase.'"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$" autocomplete="off"';
                if($field[0]->remote != '') { $html .= " data-remote='".$field[0]->remote."'"; }
                $html .= ">";
                if($field[0]->label != "") $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a password field
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getPasswordField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->onkeyup != "" ? $onkeyup = " onkeyup='".$field[0]->onkeyup."'" : $onkeyup = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                if($field[0]->label != "") $html .= "<div class='controls'>";
                $html .= "<input type='".$field[0]->type."' name='".$field[0]->name."' style='display:none;' />";
                $html .= "<input type='".$field[0]->type."' data-minlength='".$field[0]->minlength."' id='".$field[0]->id."' value='".$default."' name='".$field[0]->name."'";
                if($field[0]->match != '') { $html .= "data-match='".$field[0]->match."' "; }
                $html .= $disabled.' '.$required.' '.$onchange.$onkeyup.' placeholder="'.$lang->get($field[0]->placeholder).'" class="form-control '.$field[0]->clase.'" autocomplete="off"';
                $html .= ">";
                if($field[0]->label != "") $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a input box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getDateField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
				$field[0]->readonly == 'true' ? $readonly = "readonly='true'" : $readonly = "";
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<div class='input-group' id='".$field[0]->id."-icon'>";
                $html .= "<input type='date' id='".$field[0]->id."' value='".$default."' name='".$field[0]->name."'";
                $html .= $disabled." ".$readonly." ".$required." class='form-control' autocomplete='off'>";
                $html .= "<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>";
                $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a input box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getDateTimeField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
				$field[0]->readonly == 'true' ? $readonly = "readonly='true'" : $readonly = "";
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<div class='input-group' id='".$field[0]->id."-icon'>";
                $html .= "<input type='time' id='".$field[0]->id."' value='".$default."' name='".$field[0]->name."'";
                $html .= $disabled." ".$readonly." ".$required." class='form-control' autocomplete='off'>";
                $html .= "<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>";
                $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a input box with a colorpicker
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getColorField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = " onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->onkeyup != "" ? $onkeyup = " onkeyup='".$field[0]->onkeyup."'" : $onkeyup = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<div class='controls'>";
                $html .= "<input type='".$field[0]->type."' name='".$field[0]->name."' id='".$field[0]->id."' value='".$default."'";
                $html .= $disabled.' '.$onchange.' '.$onkeyup.' '.$required.' class="form-control">';
                $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a input box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete input field html
    */
    function getTextareaField($form, $name, $default='')
    {
        $app    = factory::getApplication();
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            //text inputs...
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";

                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                if($field[0]->label != "") $html .= "<div class='controls'>";
                $html .= "<textarea id='".$field[0]->id."' maxlength='".$field[0]->maxlength."' placeholder='".$field[0]->placeholder."' name='".$field[0]->name."' rows='".$field[0]->rows."' cols='".$field[0]->cols."' class='form-control' ".$required." ".$disabled." ".$onchange.">".$default."</textarea>";
                //$html .= "<span id='".$field[0]->name."-msg'></span>";
                if($field[0]->label != "") $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }

        return $html;
    }


    /**
     * Method to render a form button
     * @param $form string the form name
     * @param $name string the field name
     * @return $html string a complete html button
    */
    function getButton($form, $name)
    {
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onclick != "" ? $onclick = "onclick='".$field[0]->onclick."'" : $onclick = "";
                $field[0]->type == 'submit' ? $type = "type='".$field[0]->type."'" : $type = "";
                $html .= "<button $type id='".$field[0]->id."' ".$disabled." ".$onclick." class='btn btn-".$field[0]->color." ".$field[0]->clase."'>".$lang->get($field[0]->value)."</button>";
            }
        }
        return $html;
    }

    /**
     * Method to render a repeatable field require jquery ui
     * @param $form string the form name
     * @param $fields array of field names
	 * @param $tmpl array of default values
	 * @param $list object to fill the list field
	 * @param $value string the value field for list fields
	 * @param $key string the key field for list fields
	 * @see https://github.com/Rhyzz/repeatable-fields
     * @return $html string a complete repeatable field
    */
    public function getRepeatable($form, $fields, $tmpl=null, $list, $value, $key)
    {
        $lang   = factory::getLanguage();

        $html = "<div class='repeatable'>";
		$html .= "<table class='wrapper' width='100%'>";
		$html .= "<thead><tr><td width='10%' valign='bottom' colspan='4'><span class='add btn btn-success'><i class='bi bi-plus-lg'></i></span></td></tr></thead>";
		$html .= "<tbody class='container'>";

		$html .= "<tr class='template fila'>";

		foreach($fields as $field) {
			foreach($this->getForm($form) as $row) {
				if($row['name'] == $field) {
				$row[0]->width == '' ? $width = '40%' : $width = $row[0]->width;
                if($row[0]->type == 'hidden') { $html .= $this->getTextField($form, $field); }
				if($row[0]->type == 'text') { $html .= "<td width='".$width."'>".$this->getTextField($form, $field)."</td>"; }
                if($row[0]->type == 'date') { $html .= "<td width='".$width."'>".$this->getDateField($form, $field)."</td>"; }
                if($row[0]->type == 'datetime') { $html .= "<td width='".$width."'>".$this->getDateTimeField($form, $field)."</td>"; }
				if($row[0]->type == 'textarea') { $html .= "<td width='".$width."'>".$this->getTextareaField($form, $field)."</td>"; }
				if($row[0]->type == 'list') { $html .= "<td width='".$width."'>".$this->getListField($form, $field, "", $list, $value, $key)."</td>"; }
				if($row[0]->type == 'checkbox') { $html .= "<td width='".$width."'>".$this->getCheckboxField($form, $field)."</td>"; }
				if($row[0]->type == 'radio') { $html .= "<td width='".$width."'>".$this->getRadioField($form, $field)."</td>"; }
				}
			}
		}


		$html .= '<td valign="bottom" width="10%" align="right"><div class="mb-3"><span class="remove btn btn-danger"><i class="bi bi-trash-fill"></i></span></div></td></tr>';

		if($tmpl != null) {
            $i = 0;
			foreach($tmpl as $item) {
				$html .= "<tr class='fromdb fila row".$i."'>";
				
				foreach($fields as $field) {
					foreach($this->getForm($form) as $row) {
						if($row['name'] == $field) {
						$row[0]->width == '' ? $width = '40%' : $width = $row[0]->width;
                        if($row[0]->type == 'hidden') { $html .= $this->getTextField($form, $field, $item->$field); }
						if($row[0]->type == 'text') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getTextField($form, $field, $item->$field)."</td>"; }
                        if($row[0]->type == 'date') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getDateField($form, $field, $item->$field)."</td>"; }
                        if($row[0]->type == 'datetime') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getDateTimeField($form, $field, $item->$field)."</td>"; }
						if($row[0]->type == 'textarea') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getTextareaField($form, $field, $item->$field)."</td>"; }
						if($row[0]->type == 'list') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getListField($form, $field, $item->$field, $list, $value, $key)."</td>"; }
						if($row[0]->type == 'checkbox') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getCheckboxField($form, $field, $item->$field)."</td>"; }
						if($row[0]->type == 'radio') { $html .= "<td class='cell".$i."' width='".$width."'>".$this->getRadioField($form, $field, $item->$field)."</td>"; }
						}
					}
				}
				$html .= '<td width="10%" valign="bottom" align="right"><div class="mb-3"><span class="remove btn btn-danger" data-cell="'.$i.'"><i class="bi bi-trash-fill"></i></span></div></td></tr>';
                $i++;
			}

		} else {
			$html .= "<tr class='row0'>";
			
			foreach($fields as $field) {
				foreach($this->getForm($form) as $row) {
					if($row['name'] == $field) {
					$row[0]->width == '' ? $width = '40%' : $width = $row[0]->width;
                    if($row[0]->type == 'hidden') { $html .= $this->getTextField($form, $field); }
					if($row[0]->type == 'text') { $html .= "<td class='cell0' width='".$width."'>".$this->getTextField($form, $field)."</td>"; }
                    if($row[0]->type == 'date') { $html .= "<td class='cell0' width='".$width."'>".$this->getDateField($form, $field)."</td>"; }
                    if($row[0]->type == 'datetime') { $html .= "<td class='cell0' width='".$width."'>".$this->getDateTimeField($form, $field)."</td>"; }
					if($row[0]->type == 'textarea') { $html .= "<td class='cell0' width='".$width."'>".$this->getTextareaField($form, $field)."</td>"; }
					if($row[0]->type == 'list') { $html .= "<td class='cell0' width='".$width."'>".$this->getListField($form, $field, "", $list, $value, $key)."</td>"; }
					if($row[0]->type == 'checkbox') { $html .= "<td class='cell0' width='".$width."'>".$this->getCheckboxField($form, $field)."</td>"; }
					if($row[0]->type == 'radio') { $html .= "<td class='cell0' width='".$width."'>".$this->getRadioField($form, $field)."</td>"; }
					}
				}
			}

			$html .= '<td width="10%" valign="bottom" align="right"><div class="mb-3"><span class="remove btn btn-danger" data-cell="0"><i class="bi bi-trash-fill"></i></span></div></td>';
		}
		$html .= "<script>";
		$html .= '$(document).ready(function () {';
		$html .= '$(".repeatable").each(function() {';
		$html .= '$(this).repeatable_fields();';
		$html .= '});';
		$html .= '});';
		$html .= "</script>";
		$html .= '</tr></tbody></table>';
		$html .= '</div>';
        return $html;
    }

    /**
     * Method to render a usergroups select box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
    */
    function getUsergroupsField($form, $name, $default='')
    {
        $lang   = factory::getLanguage();
        $db     = factory::getDatabase();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label class='control-label' for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<select id='".$field[0]->id."' name='".$field[0]->name."' data-message='".$lang->get($field[0]->message)."' ".$required." ".$onchange." class='".$class." form-control' ".$disabled.">";

                $db->query('SELECT * FROM #_usergroups');
                $rows = $db->fetchObjectList();

                $html .= "<option value='0'>".$lang->get('CW_SELECT_USERGROUP')."</option>";

				foreach($rows as $row) {
					  $default == $row->id ? $selected = "selected='selected'" : $selected = "";
					  $html .= "<option value='".$row->id."' $selected>".$row->usergroup."</option>";
				}

                $html .= "</select>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a users select box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
    */
    function getUsersField($form, $name, $default='')
    {
        $lang   = factory::getLanguage();
        $db     = factory::getDatabase();
        $user   = factory::getUser();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                if(isset($field[0]->multiple)){
                    $multiple[0] = "multiple ";
                    $multiple[1] = "[]";
                    $multiple[2] = "custom-select";
                    $multiple[3] = " size='4'";
                }else{
                    $multiple[0] = "";
                    $multiple[1] = "";
                    $multiple[2] = "";
                    $multiple[3] = "";
                }
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label class='control-label' for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<select id='".$field[0]->id."' ".$multiple[0]." name='".$field[0]->name.$multiple[1]."' data-message='".$lang->get($field[0]->message)."' ".$onchange." ".$required." class='".$class." ".$multiple[2]." form-control' ".$disabled." ".$multiple[3].">";

                $db->query('SELECT id, username FROM #_users');
                $rows = $db->fetchObjectList();

                $html .= "<option value=''>".$lang->get('CW_SELECT_USER')."</option>";


                if(isset($field[0]->multiple)) { 
                    $default = explode(',', $default);
                    foreach($rows as $row) {
                        in_array($row->id,$default) ? $selected = "selected='selected'" : $selected = "";
                        $html .= "<option value='".$row->id."' $selected>".$row->username."</option>";
                    }
                } else {
                    foreach($rows as $row) {
                        $default == '' ? $default = $user->id : $default = $default;
                        $default == $row->id ? $selected = "selected='selected'" : $selected = "";
                        $html .= "<option value='".$row->id."' $selected>".$row->username."</option>";
                    }
                }

                $html .= "</select>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a select box
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @param $options array optional array of options
     * @return $html string a complete select field html
    */
    function getListField($form, $name, $default='', $options=null, $key='', $value='')
    {
        $lang   = factory::getLanguage();
        $db     = factory::getDatabase();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                if(isset($field[0]->button)){
                    $input[0] = "input-group mb-3";
                    $input[1] = "custom-select";
                }else{
                    $input[0] = "mb-3";
                    $input[1] = "";
                }
                $iconButton = isset($field[0]->iconButton) ? $field[0]->iconButton : "fa fa-plus";
                $buttonId = isset($field[0]->buttonId) ? $field[0]->buttonId : "";
                if(isset($field[0]->multiple)){
                    $multiple[0] = "multiple ";
                    $multiple[1] = "[]";
                    $multiple[2] = "custom-select";
                    $multiple[3] = " size='4'";
                }else{
                    $multiple[0] = "";
                    $multiple[1] = "";
                    $multiple[2] = "";
                    $multiple[3] = "";
                }
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='".$input[0]."'>";
                if($field[0]->label != "") $html .= "<label class='control-label' for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<select id='".$field[0]->id."' name='".$field[0]->name . $multiple[1] ."' ".$required." ". $onchange . $multiple[0] ." class='form-select ".$field[0]->classe." ". $class ." ". $input[1] ." ".$field[0]->classe. " ".  $multiple[2] ." form-control' ".$disabled. $multiple[3] .">";

                //primer els options del xml
                foreach($field[0]->option as $option) {
                    $default == $option['value'] ? $selected = "selected='selected'" : $selected = "";
                    $option['onclick'] != '' ? $click = "onclick='".$option['onclick']."'" : $click = "";
                    $html .= "<option value='".$option['value']."' $click $selected>".$lang->get($option[0])."</option>";
                }

                //si passem una funció per crear els options
				if($options != null) {

					foreach($options as $option) {
						if($key == '' && $value == '') {
							$default == $option->$name ? $selected = "selected='selected'" : $selected = "";
							$html .= "<option value='".$option->$name."' $selected>".$option->$name."</option>";
						} else {
							$default == $option->$value ? $selected = "selected='selected'" : $selected = "";
							$html .= "<option value='".$option->$value."' $selected>".$option->$key."</option>";
						}
					}
				}
                else {
                    //si el xml porta el seu propi query
                    if($field[0]->query != '') {
                        $query = str_replace('{userid}', $user->id, $field[0]->query);
                        $db->query($query);
                        $options = $db->fetchObjectList();
                        $value = $field[0]->value;
                        $key = $field[0]->key;
                        foreach($options as $option) {
                            if($field[0]->multiple == true && $default != '*') {
                                $defaults = explode(',', $default);
                                if(in_array($option->$key, $defaults)) {
                                    $selected = "selected='selected'";
                                } else {
                                    $selected = '';
                                }
                            } else {
                                $default == $option->$key ? $selected = "selected='selected'" : $selected = "";
                            }
                            $html .= "<option value='".$option->$key."' $selected>".$option->$value."</option>";
                        }
                    }
                }
            
                $html .= "</select>";
                if(isset($field[0]->button)){
                    $html .= "<div class='input-group-append'>
                                <button id='". $buttonId ."' class='btn btn-outline-secondary' type='button'><i class='".$iconButton."' aria-hidden='true'></i></button>
                            </div>";
                }

                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

 	/**
     * Method to render a checkbox
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete checkbox field html
    */
    function getCheckboxField($form, $name, $default='')
    {
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->onclick != "" ? $onclick = "onclick='".$field[0]->onclick."'" : $onclick = "";
                $html .= "<div class='form-check'>";
                foreach($field[0]->option as $option) {
                    $default == $option['value'] ? $checked = "checked='checked';" : $checked = "";
                    $html .= "<input type='checkbox' class='form-check-input' name='".$field[0]->name."' id='".$field[0]->id."' value='".$option['value']."' ".$onclick.">";
                    $html .= "<label class='form-check-label' for='".$field[0]->name."'>".$lang->get($option[0])."</label>";
                }
                $html .= "</div>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a radio
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete radio field html
    */
    function getRadioField($form, $name, $default='')
    {
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->onclick != "" ? $onclick = "onclick='".$field[0]->onclick."'" : $onclick = "";
                $html .= "<div id='".$field[0]->name."-field' class='form-check'>";

        	//$html .= "<div class='col-sm-9'>";
                $html .= " <div class='btn-group ".$name."' data-toggle='buttons'>";

                foreach($field[0]->option as $option) {
                    $default == $option['value'] ? $checked = "checked='checked'" : $checked = "";
					$default == $option['value'] ? $class = "active" : $class = "";
					$html .= "<input type='radio' name='".$field[0]->name."' id='".$field[0]->id."' ".$checked." value='".$option['value']."' ".$onclick."  class='form-check-input'>";
					if($field[0]->label != "") $html .= "<label class='form-check-label ".$class."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($option[0])."</a></label> ";
                }

                //$html .= "</div>";
				$html .= "</div>";
				$html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a switch field
     * @param $form string the form name
     * @param $name string the field name
     * @param $default mixed optional default value
     * @return $html string a complete radio field html
    */
    function getSwitchField($form, $name, $default='')
    {
        $lang   = factory::getLanguage();

        $html = "";

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->onclick != "" ? $onclick = "onclick='".$field[0]->onclick."'" : $onclick = "";
                $html .= "<div id='".$field[0]->name."-field' class='form-check form-switch'>";

                foreach($field[0]->option as $option) {
                    $default == $option['value'] ? $checked = "checked='checked'" : $checked = "";
					$default == $option['value'] ? $checked = "checked" : $checked = "";
					$html .= "<input type='checkbox' name='".$field[0]->name."' id='".$field[0]->id."' ".$checked." value='".$option['value']."' ".$onclick."  class='form-check-input'>";
					if($field[0]->label != "") $html .= "<label class='form-check-label'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($option[0])."</a></label> ";
                }

				$html .= "</div>";
				$html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a filelist field
     * @param $form string the form name
     * @param $name string the field name
     * @param $name string the folder path
     * @param $default mixed optional default value
     * @return $html string a complete filelist field html
    */
    function getFiles($form, $name, $folder, $default='')
    {
    	$lang   = factory::getLanguage();

        $html = "";

		$dir = opendir($folder);
		while (false !== ($file = readdir($dir))) {
			if( $file != "." && $file != "..") {
				$ficheros[] = $file;
			}
		}
		closedir($dir);

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label class='control-label' for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label)."</a></label>";
                $html .= "<select multiple data-role='tagsinput' id='".$field[0]->id."' name='".$field[0]->name."' data-message='".$lang->get($field[0]->message)."' ".$onchange." ".$disabled.">";

				for($i=0; $i<count( $ficheros ); $i++) {
					  $default == $option[$i] ? $selected = "selected='selected'" : $selected = "";
					  $html .= "<option value='".$option[$i]."' $selected>".$option[$i]."</option>";
				}

                $html .= "</select>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }

    /**
     * Method to render a filelist field
     * @param $form string the form name
     * @param $name string the field name
     * @param $name string the folder path
     * @param $default mixed optional default value
     * @return $html string a complete filelist field html
    */
    public function getViewsField($form, $name, $default='*')
    {
    	$lang   = factory::getLanguage();
        $db     = factory::getDatabase();
        $app    = factory::getApplication();

        $id     = $app->getVar('id', 0);

        $html = "";

        $scan = scandir(CWPATH_COMPONENT.DS.'views');
        foreach($scan as $folder) {
            if ($folder !== '.' && $folder !== '..') {
                $folders[] = $folder;
            }
        }
        //print_r($folders);

        foreach($this->getForm($form) as $field) {
            if($field['name'] == $name) {
                $field[0]->disabled == 'true' ? $disabled = "disabled='disabled'" : $disabled = "";
                $field[0]->onchange != "" ? $onchange = "onchange='".$field[0]->onchange."'" : $onchange = "";
                $field[0]->required == 'true' ? $required = "required='true'" : $required = "";
                $field[0]->required == 'true' ? $star = " (*)" : $star = "";
                $html .= "<div id='".$field[0]->name."-field' class='mb-3'>";
                if($field[0]->label != "") $html .= "<label class='control-label' for='".$field[0]->id."'><a class='hasTip' title='".$lang->get($field[0]->placeholder)."'>".$lang->get($field[0]->label).$star."</a></label>";
                $html .= "<select multiple id='".$field[0]->id."' class='form-control' name='".$field[0]->name."[]' data-message='".$lang->get($field[0]->message)."' ".$onchange." ".$disabled.">";
                $default == '*' ? $selected = 'selected' : $selected = '';
                $html .= "<option value='*' $selected>Tots</option>";
                foreach($folders as $folder) {
                    $html .= "<optgroup label='".ucfirst($folder)."'>";

                    $ficheros = array_diff(scandir(CWPATH_COMPONENT.DS.'views'.DS.$folder.DS.'tmpl'), array('.', '..'));

                    $db->query('SELECT views FROM `#_usergroups` WHERE id = '.$id);
                    $views = explode(',', $db->loadResult());

                    foreach($ficheros as $fichero) {
                        $file = str_replace('.php', '', $fichero);
                        in_array($file, $views) ? $selected = "selected='selected'" : $selected = "";
                        $html .= "<option value='".$file."' $selected>".$file."</option>";
                    }

                    $html .= "</optgroup>";
                }

                $html .= "</select>";
                $html .= "<div id='".$field[0]->name."Help' class='form-text'>".$lang->get($field[0]->message)."</div><div class='invalid-feedback'>".$lang->get($field[0]->invalid)."</div></div>";
            }
        }
        return $html;
    }
}
