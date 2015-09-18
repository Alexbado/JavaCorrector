<?php
require_once($CFG->libdir.'/gradelib.php');
$file = $DB->get_record ( 'jcode_files', array (
		'user_id' => $USER->id,
		'jcode_id' => $jcode->id 
) );
// verifica se o arquivo ainda nao foi enviado
if (empty ( $file )) {
	if (has_capability ( 'mod/jcode:submit', $context )) {
		require_once 'submission_form.php';
		$form = new mod_submission_form ( array (
				"course" => $course->id,
				"jcode" => $id 
		) );
		
		if ($data = $form->get_data ()) {
			// manipulando arquivo
			$filename = $form->get_new_filename ( 'userfile' );
			// criando pastas necessarias para arquivamento
			if (! is_dir ( $CFG->dataroot . '/mod' )) {
				mkdir ( $CFG->dataroot . '/mod' );
				mkdir ( $CFG->dataroot . '/mod/jcode' );
			}
			// cria o diretorio que representa o curso no Moodledata
			if (! is_dir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id )) {
				mkdir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id );
			}
			// cria o diretorio que representa a atividade no Moodledata
			if (! is_dir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id )) {
				mkdir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id );
			}
			
			// cria o diretorio que representa o usuário no Moodledata
			if (! is_dir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id . "/" . $USER->id )) {
				mkdir ( $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id . "/" . $USER->id );
			}
			
			// local onde o arquivo será gravado
			$save_path = $CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id . "/" . $USER->id . '/';
			// movendo o arquivo
			$success = $form->save_file ( 'userfile', $save_path . $filename, true );
			
			// compilando
			system ( "javac $save_path$filename", $resultado );
			if ($resultado == "1") {
				echo "Erro ao compilar arquivo";
			}
			
			$file_info = pathinfo ( $filename );
			// esecutando o arquivo
			exec ( "cd $save_path && java {$file_info['filename']} {$jcode->parameters} -cp 2>&1", $out, $success );
			$response = current ( $out );
			$out = serialize ( $out );
			
			
			// salvando registro
			if ($success == 0) {
				$post_file = new stdClass ();
				$post_file->jcode_id = $jcode->id;
				$post_file->filename = $filename;
				$post_file->user_id = $USER->id;
				$post_file->submit_date = time ();
				if ($jcode->correctanswer == $response) {
					$post_file->grade = $jcode->grade;
				} else {
					$post_file->grade = 0;
				}
				$post_file->result = $out;
				$DB->insert_record ( 'jcode_files', $post_file );
				
				$grading_info = grade_get_grades($COURSE->id, 'mod', 'jcode', $jcode->id, 0);
				grade_update_outcomes('mod/jcode', $COURSE->id, 'mod', 'jcode', $jcode->id, $post_file->user_id, array('0'=>$post_file->grade));
			}
			
			$url = $CFG->wwwroot . "/mod/jcode/view.php?id=" . $id;
			redirect ( $url, "Processando arquivo, aguarde por favor..." );
		} else {
			$form->display ();
		}
	}
} else {
	$o = $OUTPUT->box_start ( 'boxaligncenter submissionsummarytable' );
	$t = new html_table ();
	
	//data de envio
	$remove = '';
	if($jcode->resubmit && $jcode->timedue >= time()){
		$remove = " <a href='{$CFG->wwwroot}/mod/jcode/view.php?id=$id&remove={$file->id}' >".get_string('remove', 'jcode')."</a>";
	}
	jcode_add_table_row_tuple ( $t, get_string ( 'submit_date', 'jcode' ),  userdate ( $file->submit_date) .$remove );
	
	
	//data de inicio
	if($jcode->timeavaliable == 0){
		$jcode->timeavaliable = get_string('disabled', 'jcode');
	}else{
		$jcode->timeavaliable = userdate ( $jcode->timeavaliable );
	}
	jcode_add_table_row_tuple ( $t, get_string ( 'timeavaliable', 'jcode' ), $jcode->timeavaliable);
	
	
	//data de fim
	if($jcode->timedue == 0){
		$jcode->stimedue = get_string('disabled', 'jcode');
	}else{
		$jcode->stimedue = userdate ( $jcode->timedue );
	}
	jcode_add_table_row_tuple ( $t, get_string ( 'timedue', 'jcode' ), $jcode->stimedue);
	
	if($jcode->timedue <= time()){
		jcode_add_table_row_tuple ( $t, get_string ( 'grade', 'jcode' ),  $file->grade  );
		jcode_add_table_row_tuple ( $t, get_string ( 'feedback', 'jcode' ), $file->feedback  );
	}
	
	
	//alerta de remoção de arquivo
	$remove_file = optional_param ( 'remove', 0, PARAM_INT );
	$remove_confirm = optional_param ( 'remove_confirm', 0, PARAM_TEXT);
	if($remove_confirm === get_string('yes')){
		$DB->delete_records('jcode_files', array("id" => $file->id));
		$url = new moodle_url('/mod/jcode/view.php', array('id' => $id));
		redirect($url,  get_string('removed', 'jcode'));
	}
	if($remove_file > 0 ){
		$o .= get_string('remove_alert', 'jcode')."<br/>";
		$o .=  "<form method='post'>";
		$o .= '<input type="submit" value="'.get_string('yes').'" name="remove_confirm">';
		$o .= '<input type="submit" value="'.get_string('no').'" name="remove">';
		$o .= '</form>';
	}
	
	$o .= $OUTPUT->box_end ();
	echo html_writer::table ( $t );
	echo $o;
}



