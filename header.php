<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: header.php

header ("Content-type: text/html; charset=utf-8");
header ("X-UA-Compatible: IE=Edge, chrome=1");
header ("Cache-Control: public"); // Tell the client (and any caches) that this information can be stored in public caches.
header ("Connection: Keep-Alive"); // Tell the client to keep going until it gets all data, please.
header ("Vary: Accept-Encoding, Accept-Language");
header ("Keep-Alive: timeout=15, max=100");

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('common', 'mailto'));

$variables = null;
$variables['lang'] = $lang;

// Body class defines a css file for a specific page, if one isn't defined, it defaults to bnt, which is
// nulled by the template.
if (!isset ($body_class))
{
    $body_class = "bnt";
}
$variables['body_class'] = $body_class;

if (isset ($title))
{
    $variables['title'] = $title;
}

// Some pages (like mailto) include ckeditor js, check if this is one of those.
if (isset ($include_ckeditor))
{
    $variables['include_ckeditor'] = true;
}
else
{
    $variables['include_ckeditor'] = false;
}

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvars";

$template->AddVariables ('langvars', $langvars);
$template->AddVariables ('variables', $variables);
$template->Display ("header.tpl");
?>
