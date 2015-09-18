<?php
$file = $DB->get_record('jcode_files', array('id' =>$download));
header('Content-type: application/force-download');
header('Content-Transfer-Encoding: Binary');
header("Content-disposition: attachment; filename={$file->filename}");
readfile($CFG->dataroot . '/mod/jcode/' . $COURSE->id . "/" . $jcode->id . "/" . $file->user_id . '/'. $file->filename);
die;