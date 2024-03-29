<?php
/* $Id: tbl_alter.php,v 2.17 2005/04/13 14:44:14 lem9 Exp $ */
// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Gets some core libraries
 */
require_once('./libraries/grab_globals.lib.php');
$js_to_run = 'functions.js';
require_once('./header.inc.php');

// Check parameters
PMA_checkParameters(array('db', 'table'));

/**
 * Gets tables informations
 */
require('./tbl_properties_common.php');
require('./tbl_properties_table_info.php');
/**
 * Displays top menu links
 */
$active_page = 'tbl_properties_structure.php';
// I don't see the need to display the links here, they will be displayed later
//require('./tbl_properties_links.php');


/**
 * Defines the url to return to in case of error in a sql statement
 */
$err_url = 'tbl_properties_structure.php?' . PMA_generate_common_url($db, $table);


/**
 * Modifications have been submitted -> updates the table
 */
$abort = false;
if (isset($do_save_data)) {
    $field_cnt = count($field_orig);
    for ($i = 0; $i < $field_cnt; $i++) {
        // to "&quot;" in tbl_properties.php
        $field_orig[$i]     = urldecode($field_orig[$i]);
        if (strcmp(str_replace('"', '&quot;', $field_orig[$i]), $field_name[$i]) == 0) {
            $field_name[$i] = $field_orig[$i];
        }
        $field_default_orig[$i] = urldecode($field_default_orig[$i]);
        if (strcmp(str_replace('"', '&quot;', $field_default_orig[$i]), $field_default[$i]) == 0) {
            $field_default[$i]  = $field_default_orig[$i];
        }
        $field_length_orig[$i] = urldecode($field_length_orig[$i]);
        if (strcmp(str_replace('"', '&quot;', $field_length_orig[$i]), $field_length[$i]) == 0) {
            $field_length[$i] = $field_length_orig[$i];
        }
        if (!isset($query)) {
            $query = '';
        } else {
            $query .= ', CHANGE ';
        }

        $full_field_type = $field_type[$i];
        if ($field_length[$i] != ''
            && !preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i', $field_type[$i])) {
            $full_field_type .= '(' . $field_length[$i] . ')';
        }
        if ($field_attribute[$i] != '') {
            $full_field_type .= ' ' . $field_attribute[$i];
        }
        // take care of native MySQL comments here

        $query .= PMA_generateAlterTable($field_orig[$i], $field_name[$i], $full_field_type, (PMA_MYSQL_INT_VERSION >= 40100 && $field_collation[$i] != '' ? $field_collation[$i] : ''), $field_null[$i], $field_default[$i], (isset($field_default_current_timestamp[$i]) ? $field_default_current_timestamp[$i] : ''), $field_extra[$i], (PMA_MYSQL_INT_VERSION >= 40100 && isset($field_comments[$i]) && $field_comments[$i] != '' ? $field_comments[$i] : ''));
    } // end for

    // To allow replication, we first select the db to use and then run queries
    // on this db.
     PMA_DBI_select_db($db) or PMA_mysqlDie(PMA_DBI_getError(), 'USE ' . PMA_backquote($db) . ';', '', $err_url);
    // Optimization fix - 2 May 2001 - Robbat2
    $sql_query = 'ALTER TABLE ' . PMA_backquote($table) . ' CHANGE ' . $query;
    $error_create = FALSE;
    $result    = PMA_DBI_try_query($sql_query) or $error_create = TRUE;

    if ($error_create == FALSE) {
        $message   = $strTable . ' ' . htmlspecialchars($table) . ' ' . $strHasBeenAltered;
        $btnDrop   = 'Fake';

        // garvin: If comments were sent, enable relation stuff
        require_once('./libraries/relation.lib.php');
        require_once('./libraries/transformations.lib.php');

        $cfgRelation = PMA_getRelationsParam();

        // take care of pmadb internal comments here
        // garvin: Update comment table, if a comment was set.
        if (PMA_MYSQL_INT_VERSION < 40100 && isset($field_comments) && is_array($field_comments) && $cfgRelation['commwork']) {
            foreach ($field_comments AS $fieldindex => $fieldcomment) {
                PMA_setComment($db, $table, $field_name[$fieldindex], $fieldcomment, $field_orig[$fieldindex], 'pmadb');
            }
        }

        // garvin: Rename relations&display fields, if altered.
        if (($cfgRelation['displaywork'] || $cfgRelation['relwork']) && isset($field_orig) && is_array($field_orig)) {
            foreach ($field_orig AS $fieldindex => $fieldcontent) {
                if ($field_name[$fieldindex] != $fieldcontent) {
                    if ($cfgRelation['displaywork']) {
                        $table_query = 'UPDATE ' . PMA_backquote($cfgRelation['table_info'])
                                      . ' SET     display_field = \'' . PMA_sqlAddslashes($field_name[$fieldindex]) . '\''
                                      . ' WHERE db_name  = \'' . PMA_sqlAddslashes($db) . '\''
                                      . ' AND table_name = \'' . PMA_sqlAddslashes($table) . '\''
                                      . ' AND display_field = \'' . PMA_sqlAddslashes($fieldcontent) . '\'';
                        $tb_rs    = PMA_query_as_cu($table_query);
                        unset($table_query);
                        unset($tb_rs);
                    }

                    if ($cfgRelation['relwork']) {
                        $table_query = 'UPDATE ' . PMA_backquote($cfgRelation['relation'])
                                      . ' SET     master_field = \'' . PMA_sqlAddslashes($field_name[$fieldindex]) . '\''
                                      . ' WHERE master_db  = \'' . PMA_sqlAddslashes($db) . '\''
                                      . ' AND master_table = \'' . PMA_sqlAddslashes($table) . '\''
                                      . ' AND master_field = \'' . PMA_sqlAddslashes($fieldcontent) . '\'';
                        $tb_rs    = PMA_query_as_cu($table_query);
                        unset($table_query);
                        unset($tb_rs);

                        $table_query = 'UPDATE ' . PMA_backquote($cfgRelation['relation'])
                                      . ' SET     foreign_field = \'' . PMA_sqlAddslashes($field_name[$fieldindex]) . '\''
                                      . ' WHERE foreign_db  = \'' . PMA_sqlAddslashes($db) . '\''
                                      . ' AND foreign_table = \'' . PMA_sqlAddslashes($table) . '\''
                                      . ' AND foreign_field = \'' . PMA_sqlAddslashes($fieldcontent) . '\'';
                        $tb_rs    = PMA_query_as_cu($table_query);
                        unset($table_query);
                        unset($tb_rs);
                    } // end if relwork
                } // end if fieldname has changed
            } // end while check fieldnames
        } // end if relations/display has to be changed

        // garvin: Update comment table for mime types [MIME]
        if (isset($field_mimetype) && is_array($field_mimetype) && $cfgRelation['commwork'] && $cfgRelation['mimework'] && $cfg['BrowseMIME']) {
            foreach ($field_mimetype AS $fieldindex => $mimetype) {
                PMA_setMIME($db, $table, $field_name[$fieldindex], $mimetype, $field_transformation[$fieldindex], $field_transformation_options[$fieldindex]);
            }
        }

        $active_page = 'tbl_properties_structure.php';
        require('./tbl_properties_structure.php');
    } else {
        PMA_mysqlDie('', '', '', $err_url, FALSE);
        // garvin: An error happened while inserting/updating a table definition.
        // to prevent total loss of that data, we embed the form once again.
        // The variable $regenerate will be used to restore data in tbl_properties.inc.php
        if (isset($orig_field)) {
                $field = $orig_field;
        }

        $regenerate = true;
    }
}

/**
 * No modifications yet required -> displays the table fields
 */
if ($abort == FALSE) {
    if (!isset($selected)) {
        PMA_checkParameters(array('field'));
        $selected[]   = $field;
        $selected_cnt = 1;
    } else { // from a multiple submit
        $selected_cnt = count($selected);
    }

    // TODO: optimize in case of multiple fields to modify
    for ($i = 0; $i < $selected_cnt; $i++) {
        if (!empty($submit_mult)) {
            $field = PMA_sqlAddslashes(urldecode($selected[$i]), TRUE);
        } else {
            $field = PMA_sqlAddslashes($selected[$i], TRUE);
        }
        $result        = PMA_DBI_query('SHOW FULL FIELDS FROM ' . PMA_backquote($table) . ' FROM ' . PMA_backquote($db) . ' LIKE \'' . $field . '\';');
        $fields_meta[] = PMA_DBI_fetch_assoc($result);
        PMA_DBI_free_result($result);
    }
    $num_fields  = count($fields_meta);
    $action      = 'tbl_alter.php';

    // Get more complete field information
    // For now, this is done just for MySQL 4.1.2+ new TIMESTAMP options
    // but later, if the analyser returns more information, it
    // could be executed for any MySQL version and replace
    // the info given by SHOW FULL FIELDS FROM.
    // TODO: put this code into a require()
    // or maybe make it part of PMA_DBI_get_fields();

    if (PMA_MYSQL_INT_VERSION >= 40102) {
        $show_create_table_query = 'SHOW CREATE TABLE '
            . PMA_backquote($db) . '.' . PMA_backquote($table);
        $show_create_table_res = PMA_DBI_query($show_create_table_query);
        list(,$show_create_table) = PMA_DBI_fetch_row($show_create_table_res);
        PMA_DBI_free_result($show_create_table_res);
        unset($show_create_table_res, $show_create_table_query);
        $analyzed_sql = PMA_SQP_analyze(PMA_SQP_parse($show_create_table));
    }

    require('./tbl_properties.inc.php');
}


/**
 * Displays the footer
 */
require_once('./footer.inc.php');
?>
