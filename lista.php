<?php
$role = $DB->get_record ( 'role', array (
		'shortname' => 'student' 
) );

$table = 'jcode_files';
$context = context_course::instance($course->id);
$students = get_role_users ( $role->id, $context );
$save = optional_param('savequickgrades', '', PARAM_TEXT);
require_once($CFG->libdir.'/gradelib.php');

if(!empty($save)){
	foreach ($_POST as $name=>$value) {
		if(strstr($name, 'quickgrade_') && !strstr($name, 'comments_') ){
			$name = str_replace('quickgrade_','', $name );
			$nota = new stdClass();
			$nota->grade = $value;
			
			$grading_info = grade_get_grades($course->id, 'mod', 'jcode', $jcode->id, 0);
			grade_update_outcomes('mod/jcode', $course->id, 'mod', 'jcode', $jcode->id, $name, array('0'=>$value));
			if(is_numeric($nota->grade)){
				$feedback = $_POST['quickgrade_comments_'.$name];
				$nota->feedback = $feedback;
				if($n = $DB->get_record($table, array('jcode_id' =>$jcode->id , 'user_id' => $name))){
					$nota->id = $n->id;
					$DB->update_record($table, $nota);
				}else{
					$DB->insert($table, $nota);
				}
			}
		}
	}
	
}

$t = new html_table ();

jcode_add_table_row_cells ( $t, array (
		'Aluno',
		'Nota',
		'Feedback',
		'Data de Entrega',
		'Arquivo',
		'Resultado' 
) );
require_once($CFG->libdir.'/gradelib.php');
$grading_info = grade_get_grades($COURSE->id, 'mod', 'jcode', $cm->instance, $USER->id);

$grade_item_grademax = $grading_info->items[0]->grademax;

foreach ( $students as $student ) {
	$file = $DB->get_record ( 'jcode_files', array (
			'jcode_id' => $jcode->id,
			'user_id' => $student->id
	));
	
	//data da entrega
	$submit_date  = ' - ';
	if ($file) {
		$submit_date = userdate ( $file->submit_date );
	}
	
	// Nota
	$nota = ' - ';
	if ($file && $jcode->grade > 0) {
		$nota = '<label class="accesshide" for="quickgrade_' . $student->id . '">Nota do usuário</label><input id="quickgrade_' . $student->id . '" class="quickgrade" type="text" maxlength="10" size="6"  value="'.$file->grade.'" name="quickgrade_' . $student->id . '"> / ' . $jcode->grade;
	}
	
	// feedback
	$feedback = ' - ';
	if ($file && $jcode->grade > 0) {
		$feedback = '<label class="accesshide" for="quickgrade_comments_' . $student->id . '">Comentários de feedback</label>
				<textarea id="quickgrade_comments_' . $student->id . '" class="quickgrade" name="quickgrade_comments_' . $student->id . '">'.$file->feedback.'</textarea>';
	}
	//download do arquivo java
	$file_url = ' - ';
	if ($file) {
		$file_url =  "<a href='".$CFG->wwwroot . "/mod/jcode/view.php?id=" . $id."&download=".$file->id."' > $file->filename </a>";
		//$file_url = "<a href='". $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id . "/" . $file->user_id . '/'. $file->filename."' > $file->filename </a>";
	}
	
	// resultado
	if ($file) {
		$result = current(unserialize($file->result));
	} else {
		$result = '';
	}
	
	// adicionando uma linha
	jcode_add_table_row_cells ( $t, array (
			fullname ( $student ),
			$nota,
			$feedback,
			$submit_date,
			$file_url,
			$result 
	));
}
echo "<form method='post'>";
echo html_writer::table ( $t );
echo '<div class="fitem fitem_actionbuttons fitem_fsubmit" id="fitem_id_savequickgrades"><div class="felement fsubmit"><input type="submit" id="id_savequickgrades" value="Salvar todas as alterações de avaliação rápida" name="savequickgrades"></div></div></form>';
?>
