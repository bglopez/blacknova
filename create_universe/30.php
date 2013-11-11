<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: create_universe/30.php

$pos = strpos ($_SERVER['PHP_SELF'], "/30.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$create_universe_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset']            = $bntreg->default_template;
$variables['body_class']             = 'create_universe';
$variables['steps']                  = $create_universe_info['steps'];
$variables['current_step']           = $create_universe_info['current_step'];
$variables['next_step']              = $create_universe_info['next_step'];
$variables['sector_max']             = (int) filter_input (INPUT_POST, 'sector_max', FILTER_SANITIZE_NUMBER_INT); // Sanitize the input and typecast it to an int
$variables['spp']                    = filter_input (INPUT_POST, 'spp', FILTER_SANITIZE_NUMBER_INT);
$variables['oep']                    = filter_input (INPUT_POST, 'oep', FILTER_SANITIZE_NUMBER_INT);
$variables['ogp']                    = filter_input (INPUT_POST, 'ogp', FILTER_SANITIZE_NUMBER_INT);
$variables['gop']                    = filter_input (INPUT_POST, 'gop', FILTER_SANITIZE_NUMBER_INT);
$variables['enp']                    = filter_input (INPUT_POST, 'enp', FILTER_SANITIZE_NUMBER_INT);
$variables['nump']                   = filter_input (INPUT_POST, 'nump', FILTER_SANITIZE_NUMBER_INT);
$variables['empty']                  = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']            = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']            = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']                = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']                  = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']              = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$variables['destroy_schema_results'] = BntSchema::destroy ($db, $db_prefix); // Delete all tables in the database
$variables['table_count']            = count ($variables['destroy_schema_results']) - 1;
$variables['autorun']                = filter_input (INPUT_POST, 'autorun', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$variables['newlang']                = filter_input (INPUT_POST, 'newlang', FILTER_SANITIZE_URL);
$lang = $newlang; // Set the language to the language chosen during create universe

$destroy_array_size = count ($variables['destroy_schema_results']);
for ($i = 0; $i < $destroy_array_size; $i++)
{
    if ($variables['destroy_schema_results'][$i]['result'] !== true)
    {
        $variables['autorun'] = false; // We disable autorun if any errors occur in processing
    }
}

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe', 'news'));
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/create_universe/30.tpl");
?>